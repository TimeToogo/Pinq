<?php

namespace Pinq\Parsing\PHPParser;

use \Pinq\Parsing\IAST;
use \Pinq\Expressions as O;
use \Pinq\Expressions\Expression;
use \Pinq\Expressions\Operators;
use \Pinq\Parsing\ASTException;

class AST implements IAST
{
    private $Nodes = [];

    private $ConstantValueNodeReplacer;

    public function __construct(array $Nodes)
    {
        $this->Nodes = $Nodes;

        $this->InitializeVisitors();
        $this->ResolveConstantValues();
    }

    private function InitializeVisitors()
    {
        $this->ConstantValueNodeReplacer = new \PHPParser_NodeTraverser();
        $this->ConstantValueNodeReplacer->addVisitor(new Visitors\ConstantValueNodeReplacerVisitor());
    }

    /**
     * Replaces all constant value nodes to the PHPParserResolvedValueNode for easy parsing.
     */
    private function ResolveConstantValues()
    {
        $this->Nodes = $this->ConstantValueNodeReplacer->traverse($this->Nodes);
    }

    public function GetExpressions()
    {
        return $this->ParseNodes($this->Nodes);
    }

    private function ParseNodes(array $Nodes)
    {
        return array_map(function ($Node) { return $this->ParseNode($Node); }, $Nodes);
    }

    private function ParseNode(\PHPParser_Node $Node)
    {
        switch (true) {
            case $Node instanceof PHPParserResolvedValueNode:
                return $this->ParseResolvedValue($Node->Value);

            case $Node instanceof \PHPParser_Node_Stmt:
                return $this->ParseStatmentNode($Node);

            case $Node instanceof \PHPParser_Node_Expr:
                return $this->ParseExpressionNode($Node);

            //Irrelavent node, no call time pass by ref anymore
            case $Node instanceof \PHPParser_Node_Arg:
                return $this->ParseNode($Node->value);

            default:
                throw new ASTException(
                        'Unsupported node type: %s',
                        get_class($Node));
        }
    }

    private function ParseResolvedValue($Value)
    {
        return Expression::Value($Value);
    }

    final public function ParseNameNode($Node)
    {
        if ($Node instanceof \PHPParser_Node_Name || is_string($Node)) {
            $NameValue = is_string($Node) ? $Node : $Node->toString();

            return Expression::Value($NameValue);
        }

        return $this->ParseNode($Node);
    }

    final public function ParseIndexNode($Node)
    {
        if ($Node === null) {
            return Expression::Value(null);
        }

        return $this->ParseNode($Node);
    }

    // <editor-fold defaultstate="collapsed" desc="Expression node parsers">

    public function ParseExpressionNode(\PHPParser_Node_Expr $Node)
    {
        $FullNodeName = get_class($Node);
        $NodeType = str_replace('PHPParser_Node_Expr_', '', $FullNodeName);

        switch (true) {
            case $MappedNode = $this->ParseOperatorNode($Node, $NodeType):
                return $MappedNode;

            case $Node instanceof \PHPParser_Node_Expr_Array:
                return $this->ParseArrayNode($Node);

            case $Node instanceof \PHPParser_Node_Expr_FuncCall:
                return $this->ParseFunctionCallNode($Node);

            case ($Node instanceof \PHPParser_Node_Expr_New):
                return Expression::Constructor(
                        $this->ParseNameNode($Node->class),
                        $this->ParseNodes($Node->args));

            case $Node instanceof \PHPParser_Node_Expr_MethodCall:
                return Expression::MethodCall(
                        $this->ParseNode($Node->var),
                        $this->ParseNameNode($Node->name),
                        $this->ParseNodes($Node->args));

            case $Node instanceof \PHPParser_Node_Expr_PropertyFetch:
                return Expression::Field(
                        $this->ParseNode($Node->var),
                        $this->ParseNameNode($Node->name));

            case $Node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                return Expression::Index(
                        $this->ParseNode($Node->var),
                        $this->ParseIndexNode($Node->dim));

            case $Node instanceof \PHPParser_Node_Expr_StaticCall:
                return Expression::StaticMethodCall(
                        $this->ParseNameNode($Node->class),
                        $this->ParseNameNode($Node->name),
                        $this->ParseNodes($Node->args));

            case $Node instanceof \PHPParser_Node_Expr_Ternary:
                return $this->ParseTernaryNode($Node);

            case $Node instanceof \PHPParser_Node_Expr_Closure:
                return $this->ParseClosureNode($Node);

            case $Node instanceof \PHPParser_Node_Expr_Empty:
                return Expression::EmptyExpression(
                        $this->ParseNode($Node->expr));

            case $Node instanceof \PHPParser_Node_Expr_Variable:
                $NameExpression = $this->ParseNameNode($Node->name);

                return Expression::Variable($NameExpression);

            default:
                throw new ASTException(
                        'Cannot parse AST with unknown expression node: %s',
                        get_class($Node));
        }
    }

    private function ParseArrayNode(\PHPParser_Node_Expr_Array $Node)
    {
        $KeyExpressions = [];
        $ValueExpressions = [];
        foreach ($Node->items as $Key => $Item) {
            //Keys must match
            $KeyExpressions[$Key] = $Item->key === null ? null : $this->ParseNode($Item->key);
            $ValueExpressions[$Key] = $this->ParseNode($Item->value);
        }

        return Expression::ArrayExpression($KeyExpressions, $ValueExpressions);
    }

    private function ParseFunctionCallNode(\PHPParser_Node_Expr_FuncCall $Node)
    {
        $NameExpression = $this->ParseNameNode($Node->name);
        if ($NameExpression instanceof O\TraversalExpression || $NameExpression instanceof O\VariableExpression) {
            return Expression::Invocation(
                    $NameExpression,
                    $this->ParseNodes($Node->args));
        } 
        else {
            return Expression::FunctionCall(
                    $NameExpression,
                    $this->ParseNodes($Node->args));
        }
    }

    private function ParseTernaryNode(\PHPParser_Node_Expr_Ternary $Node)
    {
        return Expression::Ternary(
                $this->ParseNode($Node->cond),
                $Node->if === null ? null : $this->ParseNode($Node->if),
                $this->ParseNode($Node->else));
    }

    private function ParseClosureNode(\PHPParser_Node_Expr_Closure $Node)
    {
        $ParameterExpressions = [];
        foreach ($Node->params as $ParameterNode) {
            $ParameterExpressions[] = $this->ParseParameterNode($ParameterNode);
        }

        $UsedVariables = array_map(
                function (\PHPParser_Node_Expr_ClosureUse $Node) {
                    return $Node->var;
                },
                $Node->uses);

        $BodyExpressions = $this->ParseNodes($Node->stmts);

        return Expression::Closure($ParameterExpressions, $UsedVariables, $BodyExpressions);
    }
    
    private function ParseParameterNode(\PHPParser_Node_Param $Node) 
    {
        return Expression::Parameter(
                $Node->name, 
                $Node->type === null ? null : (string)$Node, 
                $Node->default !== null, 
                $Node->default !== null ? $this->ParseNode($Node->default) : null, 
                $Node->byRef);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Statement node parsers">

    private function ParseStatmentNode(\PHPParser_Node_Stmt $Node)
    {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Stmt_Return:
                return Expression::ReturnExpression(
                        $Node->expr !== null ? $this->ParseNode($Node->expr) : null);

            default:
                throw new ASTException(
                        'Cannot parse AST with unknown statement node: %s',
                        get_class($Node));
        }
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Operater node maps">

    private function ParseOperatorNode(\PHPParser_Node_Expr $Node, $NodeType)
    {
        switch (true) {
            case isset(self::$AssignOperatorsMap[$NodeType]):
                return Expression::Assign(
                        $this->ParseNode($Node->var),
                        self::$AssignOperatorsMap[$NodeType],
                        $this->ParseNode($Node->expr));

            case isset(self::$BinaryOperatorsMap[$NodeType]):
                return Expression::BinaryOperation(
                        $this->ParseNode($Node->left),
                        self::$BinaryOperatorsMap[$NodeType],
                        $this->ParseNode($Node->right));

            case isset(self::$UnaryOperatorsMap[$NodeType]):
                return Expression::UnaryOperation(
                        self::$UnaryOperatorsMap[$NodeType],
                        $this->ParseNode($Node->expr ?: $Node->var));

            case isset(self::$CastOperatorMap[$NodeType]):
                return Expression::Cast(
                        self::$CastOperatorMap[$NodeType],
                        $this->ParseNode($Node->expr));

            default:
                return null;
        }
    }

    private static $UnaryOperatorsMap = [
        'BitwiseNot' => Operators\Unary::BitwiseNot,
        'BooleanNot' => Operators\Unary::Not,
        'PostInc' => Operators\Unary::Increment,
        'PostDec' => Operators\Unary::Decrement,
        'PreInc' => Operators\Unary::PreIncrement,
        'PreDec' => Operators\Unary::PreDecrement,
        'UnaryMinus' => Operators\Unary::Negation,
        'UnaryPlus' => Operators\Unary::Plus,
    ];

    private static $CastOperatorMap = [
        'Cast_Array' => Operators\Cast::ArrayCast,
        'Cast_Bool' => Operators\Cast::Boolean,
        'Cast_Double' => Operators\Cast::Double,
        'Cast_Int' => Operators\Cast::Integer,
        'Cast_Object' => Operators\Cast::Object,
        'Cast_String' => Operators\Cast::String,
    ];

    private static $BinaryOperatorsMap = [
        'BitwiseAnd' => Operators\Binary::BitwiseAnd,
        'BitwiseOr' => Operators\Binary::BitwiseOr,
        'BitwiseXor' => Operators\Binary::BitwiseXor,
        'ShiftLeft' => Operators\Binary::ShiftLeft,
        'ShiftRight' => Operators\Binary::ShiftRight,
        'BooleanAnd' => Operators\Binary::LogicalAnd,
        'BooleanOr' => Operators\Binary::LogicalOr,
        'LogicalAnd' => Operators\Binary::LogicalAnd,
        'LogicalOr' => Operators\Binary::LogicalOr,
        'Plus' => Operators\Binary::Addition,
        'Minus' => Operators\Binary::Subtraction,
        'Mul' => Operators\Binary::Multiplication,
        'Div' => Operators\Binary::Division,
        'Mod' => Operators\Binary::Modulus,
        'Concat' => Operators\Binary::Concatenation,
        'Instanceof' => Operators\Binary::IsInstanceOf,
        'Equal' => Operators\Binary::Equality,
        'Identical' => Operators\Binary::Identity,
        'NotEqual' => Operators\Binary::Inequality,
        'NotIdentical' => Operators\Binary::NotIdentical,
        'Smaller' => Operators\Binary::LessThan,
        'SmallerOrEqual' => Operators\Binary::LessThanOrEqualTo,
        'Greater' => Operators\Binary::GreaterThan,
        'GreaterOrEqual' => Operators\Binary::GreaterThanOrEqualTo,
    ];

    private static $AssignOperatorsMap = [
        'Assign' => Operators\Assignment::Equal,
        'AssignBitwiseAnd' => Operators\Assignment::BitwiseAnd,
        'AssignBitwiseOr' => Operators\Assignment::BitwiseOr,
        'AssignBitwiseXor' => Operators\Assignment::BitwiseXor,
        'AssignConcat' => Operators\Assignment::Concatenate,
        'AssignDiv' => Operators\Assignment::Division,
        'AssignMinus' => Operators\Assignment::Subtraction,
        'AssignMod' => Operators\Assignment::Modulus,
        'AssignMul' => Operators\Assignment::Multiplication,
        'AssignPlus' => Operators\Assignment::Addition,
        'AssignRef' => Operators\Assignment::EqualReference,
        'AssignShiftLeft' => Operators\Assignment::ShiftLeft,
        'AssignShiftRight' => Operators\Assignment::ShiftRight,
    ];

    // </editor-fold>
}
