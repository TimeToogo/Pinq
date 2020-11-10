<?php

namespace Pinq\Parsing\PhpParser;

use PhpParser\Node;
use Pinq\Expressions as O;
use Pinq\Expressions\Expression;
use Pinq\Expressions\Operators;
use Pinq\Parsing\ASTException;

/**
 * Converts the PHP-Parser nodes into the equivalent expression tree.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AST
{
    /**
     * @var Node[]
     */
    private $nodes = [];

    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * Converts the supplied php parser nodes to an equivalent
     * expression tree.
     *
     * @param Node[] $nodes
     *
     * @return Expression[]
     */
    public static function convert(array $nodes)
    {
        return (new self($nodes))->getExpressions();
    }

    /**
     * Parses the nodes into the equivalent expression tree
     *
     * @return Expression[]
     */
    public function getExpressions()
    {
        return $this->parseNodes($this->nodes);
    }

    /**
     * @param Node[] $nodes
     *
     * @return Expression[]
     */
    private function parseNodes(array $nodes)
    {
        return array_map(
                function ($node) {
                    return $this->parseNode($node);
                },
                $nodes
        );
    }

    /**
     * @param Node $node
     *
     * @throws \Pinq\Parsing\ASTException
     * @return Expression
     */
    protected function parseNode(Node $node)
    {
        switch (true) {
            case $node instanceof Node\Stmt:
                return $this->parseStatementNode($node);

            case $node instanceof Node\Expr:
                return $this->parseExpressionNode($node);

            case $node instanceof Node\Param:
                return $this->parseParameterNode($node);

            case $node instanceof Node\Arg:
                return $this->parseArgumentNode($node);

            default:
                throw new ASTException('Unsupported node type: %s', get_class($node));
        }
    }

    /**
     * @param $node
     *
     * @return Expression
     */
    final public function parseNameNode($node)
    {
        if ($node instanceof Node\Name) {
            return Expression::value($this->parseAbsoluteName($node));
        } elseif (is_string($node) || $node instanceof Node\Identifier) {
            return Expression::value((string)$node);
        }

        return $this->parseNode($node);
    }

    protected function parseAbsoluteName(Node\Name $node)
    {
        return ($node->isFullyQualified() ? '\\' : '') . (string) $node;
    }

    private function parseParameterNode(Node\Param $node)
    {
        $type = $node->type;
        if ($type !== null) {
            $type      = (string) $type;
            $lowerType = strtolower($type);
            if ($type[0] !== '\\' && $lowerType !== 'array' && $lowerType !== 'callable') {
                $type = '\\' . $type;
            }
        }

        return Expression::parameter(
                $node->var->name,
                $type,
                $node->default === null ? null : $this->parseNode($node->default),
                $node->byRef,
                $node->variadic
        );
    }

    private function parseArgumentNode(Node\Arg $node)
    {
        return Expression::argument(
                $this->parseNode($node->value),
                $node->unpack
        );
    }

    // <editor-fold defaultstate="collapsed" desc="Expression node parsers">

    public function parseExpressionNode(Node\Expr $node)
    {
        switch (true) {
            case $mappedNode = $this->parseOperatorNode($node):
                return $mappedNode;

            case $node instanceof Node\Scalar
                    && $mappedNode = $this->parseScalarNode($node):
                return $mappedNode;

            case $node instanceof Node\Expr\Variable:
                return Expression::variable($this->parseNameNode($node->name));

            case $node instanceof Node\Expr\Array_:
                return $this->parseArrayNode($node);

            case $node instanceof Node\Expr\FuncCall:
                return $this->parseFunctionCallNode($node);

            case $node instanceof Node\Expr\New_:
                return Expression::newExpression(
                        $this->parseNameNode($node->class),
                        $this->parseNodes($node->args)
                );

            case $node instanceof Node\Expr\MethodCall:
                return Expression::methodCall(
                        $this->parseNode($node->var),
                        $this->parseNameNode($node->name),
                        $this->parseNodes($node->args)
                );

            case $node instanceof Node\Expr\PropertyFetch:
                return Expression::field(
                        $this->parseNode($node->var),
                        $this->parseNameNode($node->name)
                );

            case $node instanceof Node\Expr\ArrayDimFetch:
                return Expression::index(
                        $this->parseNode($node->var),
                        $node->dim === null ? null : $this->parseNode($node->dim)
                );

            case $node instanceof Node\Expr\ConstFetch:
                return Expression::constant($this->parseAbsoluteName($node->name));

            case $node instanceof Node\Expr\ClassConstFetch:
                return Expression::classConstant(
                        $this->parseNameNode($node->class),
                        $node->name
                );

            case $node instanceof Node\Expr\StaticCall:
                return Expression::staticMethodCall(
                        $this->parseNameNode($node->class),
                        $this->parseNameNode($node->name),
                        $this->parseNodes($node->args)
                );

            case $node instanceof Node\Expr\StaticPropertyFetch:
                return Expression::staticField(
                        $this->parseNameNode($node->class),
                        $this->parseNameNode($node->name)
                );

            case $node instanceof Node\Expr\Ternary:
                return $this->parseTernaryNode($node);

            case $node instanceof Node\Expr\Closure:
                return $this->parseClosureNode($node);

            case $node instanceof Node\Expr\Empty_:
                return Expression::emptyExpression($this->parseNode($node->expr));

            case $node instanceof Node\Expr\Isset_:
                return Expression::issetExpression($this->parseNodes($node->vars));

            default:
                throw new ASTException(
                        'Cannot parse AST with unknown expression node: %s',
                        get_class($node));
        }
    }

    private function parseArrayNode(Node\Expr\Array_ $node)
    {
        $itemExpressions = [];

        foreach ($node->items as $item) {
            //Keys must match
            $itemExpressions[] = Expression::arrayItem(
                    $item->key === null ? null : $this->parseNode($item->key),
                    $this->parseNode($item->value),
                    $item->byRef
            );
        }

        return Expression::arrayExpression($itemExpressions);
    }

    private function parseFunctionCallNode(Node\Expr\FuncCall $node)
    {
        $nameExpression = $this->parseNameNode($node->name);

        if ($nameExpression instanceof O\TraversalExpression || $nameExpression instanceof O\VariableExpression) {
            return Expression::invocation(
                    $nameExpression,
                    $this->parseNodes($node->args)
            );
        } else {
            return Expression::functionCall(
                    $nameExpression,
                    $this->parseNodes($node->args)
            );
        }
    }

    private function parseTernaryNode(Node\Expr\Ternary $node)
    {
        return Expression::ternary(
                $this->parseNode($node->cond),
                $node->if === null ? null : $this->parseNode($node->if),
                $this->parseNode($node->else)
        );
    }

    private function parseClosureNode(Node\Expr\Closure $node)
    {
        $parameterExpressions = [];

        foreach ($node->params as $parameterNode) {
            $parameterExpressions[] = $this->parseParameterNode($parameterNode);
        }

        $usedVariables = [];
        foreach ($node->uses as $usedVariable) {
            $usedVariables[] = Expression::closureUsedVariable((string)$usedVariable->var->name, $usedVariable->byRef);
        }
        $bodyExpressions = $this->parseNodes($node->stmts);

        return Expression::closure(
                $node->byRef,
                $node->static,
                $parameterExpressions,
                $usedVariables,
                $bodyExpressions
        );
    }

    private function parseScalarNode(Node\Scalar $node)
    {
        switch (true) {
            case $node instanceof Node\Scalar\DNumber:
            case $node instanceof Node\Scalar\LNumber:
            case $node instanceof Node\Scalar\String_:
                return Expression::value($node->value);

            case $node instanceof Node\Scalar\MagicConst\Line:
                return Expression::value($node->getAttribute('startLine'));

            case $node instanceof Node\Scalar\MagicConst:
                return Expression::constant($node->getName());

            default:
                return;
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">

    private function parseStatementNode(Node\Stmt $node)
    {
        switch (true) {

            case $node instanceof Node\Stmt\Return_:
                return Expression::returnExpression($node->expr !== null ? $this->parseNode($node->expr) : null);

            case $node instanceof Node\Stmt\Throw_:
                return Expression::throwExpression($this->parseNode($node->expr));

            case $node instanceof Node\Stmt\Unset_:
                return Expression::unsetExpression($this->parseNodes($node->vars));

            case $node instanceof Node\Stmt\Expression:
                return $this->parseNode($node->expr);

            default:
                $this->verifyNotControlStructure($node);
                throw new ASTException(
                        'Cannot parse AST with unknown statement node: %s',
                        get_class($node));
        }
    }

    private static $constructStructureMap = [
            'Do'       => ASTException::DO_WHILE_LOOP,
            'For'      => ASTException::FOR_LOOP,
            'Foreach'  => ASTException::FOREACH_LOOP,
            'Goto'     => ASTException::GOTO_STATEMENT,
            'If'       => ASTException::IF_STATEMENT,
            'Switch'   => ASTException::SWITCH_STATEMENT,
            'TryCatch' => ASTException::TRY_CATCH_STATEMENT,
            'While'    => ASTException::WHILE_LOOP
    ];

    private function verifyNotControlStructure(Node\Stmt $node)
    {
        $nodeType = str_replace('Stmt_', '', $node->getType());

        if (isset(self::$constructStructureMap[$nodeType])) {
            throw ASTException::containsControlStructure(
                    self::$constructStructureMap[$nodeType],
                    $node->getAttribute('startLine')
            );
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Operator node maps">

    private function parseOperatorNode(Node\Expr $node)
    {
        $nodeType = str_replace('Expr_', '', $node->getType());
        switch (true) {

            case isset(self::$assignOperatorsMap[$nodeType]):
                return Expression::assign(
                        $this->parseNode($node->var),
                        self::$assignOperatorsMap[$nodeType],
                        $this->parseNode($node->expr)
                );

            case $node instanceof Node\Expr\Instanceof_:
                return Expression::binaryOperation(
                        $this->parseNode($node->expr),
                        Operators\Binary::IS_INSTANCE_OF,
                        $this->parseNameNode($node->class)
                );

            case isset(self::$binaryOperatorsMap[$nodeType]):
                return Expression::binaryOperation(
                        $this->parseNode($node->left),
                        self::$binaryOperatorsMap[$nodeType],
                        $this->parseNode($node->right)
                );

            case isset(self::$unaryOperatorsMap[$nodeType]):
                return Expression::unaryOperation(
                        self::$unaryOperatorsMap[$nodeType],
                        $this->parseNode(isset($node->expr) ? $node->expr : $node->var)
                );

            case isset(self::$castOperatorMap[$nodeType]):
                return Expression::cast(
                        self::$castOperatorMap[$nodeType],
                        $this->parseNode($node->expr)
                );

            default:
                return null;
        }
    }

    private static $unaryOperatorsMap = [
            'BitwiseNot' => Operators\Unary::BITWISE_NOT,
            'BooleanNot' => Operators\Unary::NOT,
            'PostInc'    => Operators\Unary::INCREMENT,
            'PostDec'    => Operators\Unary::DECREMENT,
            'PreInc'     => Operators\Unary::PRE_INCREMENT,
            'PreDec'     => Operators\Unary::PRE_DECREMENT,
            'UnaryMinus' => Operators\Unary::NEGATION,
            'UnaryPlus'  => Operators\Unary::PLUS
    ];

    private static $castOperatorMap = [
            'Cast_Array'  => Operators\Cast::ARRAY_CAST,
            'Cast_Bool'   => Operators\Cast::BOOLEAN,
            'Cast_Double' => Operators\Cast::DOUBLE,
            'Cast_Int'    => Operators\Cast::INTEGER,
            'Cast_Object' => Operators\Cast::OBJECT,
            'Cast_String' => Operators\Cast::STRING
    ];

    private static $binaryOperatorsMap = [
            'BinaryOp_BitwiseAnd'     => Operators\Binary::BITWISE_AND,
            'BinaryOp_BitwiseOr'      => Operators\Binary::BITWISE_OR,
            'BinaryOp_BitwiseXor'     => Operators\Binary::BITWISE_XOR,
            'BinaryOp_ShiftLeft'      => Operators\Binary::SHIFT_LEFT,
            'BinaryOp_ShiftRight'     => Operators\Binary::SHIFT_RIGHT,
            'BinaryOp_BooleanAnd'     => Operators\Binary::LOGICAL_AND,
            'BinaryOp_BooleanOr'      => Operators\Binary::LOGICAL_OR,
            'BinaryOp_LogicalAnd'     => Operators\Binary::LOGICAL_AND,
            'BinaryOp_LogicalOr'      => Operators\Binary::LOGICAL_OR,
            'BinaryOp_Plus'           => Operators\Binary::ADDITION,
            'BinaryOp_Minus'          => Operators\Binary::SUBTRACTION,
            'BinaryOp_Mul'            => Operators\Binary::MULTIPLICATION,
            'BinaryOp_Div'            => Operators\Binary::DIVISION,
            'BinaryOp_Mod'            => Operators\Binary::MODULUS,
            'BinaryOp_Pow'            => Operators\Binary::POWER,
            'BinaryOp_Concat'         => Operators\Binary::CONCATENATION,
            'BinaryOp_Equal'          => Operators\Binary::EQUALITY,
            'BinaryOp_Identical'      => Operators\Binary::IDENTITY,
            'BinaryOp_NotEqual'       => Operators\Binary::INEQUALITY,
            'BinaryOp_NotIdentical'   => Operators\Binary::NOT_IDENTICAL,
            'BinaryOp_Smaller'        => Operators\Binary::LESS_THAN,
            'BinaryOp_SmallerOrEqual' => Operators\Binary::LESS_THAN_OR_EQUAL_TO,
            'BinaryOp_Greater'        => Operators\Binary::GREATER_THAN,
            'BinaryOp_GreaterOrEqual' => Operators\Binary::GREATER_THAN_OR_EQUAL_TO
    ];

    private static $assignOperatorsMap = [
            'Assign'              => Operators\Assignment::EQUAL,
            'AssignRef'           => Operators\Assignment::EQUAL_REFERENCE,
            'AssignOp_BitwiseAnd' => Operators\Assignment::BITWISE_AND,
            'AssignOp_BitwiseOr'  => Operators\Assignment::BITWISE_OR,
            'AssignOp_BitwiseXor' => Operators\Assignment::BITWISE_XOR,
            'AssignOp_Concat'     => Operators\Assignment::CONCATENATE,
            'AssignOp_Div'        => Operators\Assignment::DIVISION,
            'AssignOp_Minus'      => Operators\Assignment::SUBTRACTION,
            'AssignOp_Mod'        => Operators\Assignment::MODULUS,
            'AssignOp_Mul'        => Operators\Assignment::MULTIPLICATION,
            'AssignOp_Pow'        => Operators\Assignment::POWER,
            'AssignOp_Plus'       => Operators\Assignment::ADDITION,
            'AssignOp_ShiftLeft'  => Operators\Assignment::SHIFT_LEFT,
            'AssignOp_ShiftRight' => Operators\Assignment::SHIFT_RIGHT
    ];

    // </editor-fold>
}
