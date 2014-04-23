<?php

namespace Pinq\Parsing\PHPParser\Visitors;

/**
 * Attempts to locate a function node from the supplied function reflection.
 * If two ambigous function are found, it throws an exception.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionFinderVisitor extends \PHPParser_NodeVisitorAbstract
{
    private static $function = 'FUNCTION';

    private static $method = 'METHOD';

    private static $closure = 'CLOSURE';

    /**
     * The function nodes grouped by their function signature
     *
     * @var \PHPParser_Node[][]
     */
    private $functionNodes = [];

    /**
     * @var string|null
     */
    private $currentNamespace;

    /**
     * @var string|null
     */
    private $currentClass;

    public function beforeTraverse(array $nodes)
    {
        $this->functionNodes = [];
        $this->currentNamespace = null;
        $this->currentClass = null;
    }

    /**
     * @return \PHPParser_Node[][]
     */
    public function getLocatedFunctionNodesMap()
    {
        return $this->functionNodes;
    }

    private static function hash(array $data)
    {
        return implode('-', $data);
    }

    public static function functionSignatureHash(\ReflectionFunctionAbstract $reflection)
    {
        if ($reflection instanceof \ReflectionMethod) {
            return self::hash([
                self::$method,
                $reflection->getStartLine(),
                $reflection->getEndLine(),
                self::hashParameters($reflection),
                $reflection->getDeclaringClass()->getName(),
                $reflection->getName()
            ]);
        } else {
            return self::hash([
                $reflection->isClosure() ? self::$closure : self::$function,
                $reflection->getStartLine(),
                $reflection->getEndLine(),
                self::hashParameters($reflection),
                $reflection->getName()
            ]);
        }
    }

    private static function hashParameters(\ReflectionFunctionAbstract $reflection)
    {
        $hash = '';

        /* @var $Parameter \ReflectionParameter */
        foreach ($reflection->getParameters() as $parameter) {
            $hash .= $parameter->getName();
            $hash .= $parameter->isPassedByReference() ? '&' : '';
            $hash .= $parameter->isOptional();
        }

        return $hash;
    }

    private function functionNodeSignatureHash(\PHPParser_Node_Stmt_Function $node)
    {
        return self::hash([
            self::$function,
            $node->getAttribute('startLine'),
            $node->getAttribute('endLine'),
            $this->hashParamerNodes($node->params),
            $this->currentNamespace . '\\' . $node->name
        ]);
    }

    private function methodNodeSignatureHash(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        return self::hash([
            self::$method,
            $node->getAttribute('startLine'),
            $node->getAttribute('endLine'),
            $this->hashParamerNodes($node->params),
            $this->currentNamespace . '\\' . $this->currentClass,
            $node->name
        ]);
    }

    private function closureNodeSignatureHash(\PHPParser_Node_Expr_Closure $node)
    {
        //HHVM Compatibility: hhvm does not return namespace for closure name
        if (!defined('HHVM_VERSION')) {
            $name = $this->currentNamespace . '\\{closure}';
        } else {
            $name = '{closure}';
        }

        return self::hash([
            self::$closure,
            $node->getAttribute('startLine'),
            $node->getAttribute('endLine'),
            $this->hashParamerNodes($node->params),
            $name
        ]);
    }

    private function hashParamerNodes(array $nodes)
    {
        $hash = '';

        /* @var $Parameter \PHPParser_Node_Param */
        foreach ($nodes as $parameter) {
            $hash .= $parameter->name;
            $hash .= $parameter->byRef ? '&' : '';
            $hash .= $parameter->default !== null;
        }

        return $hash;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->currentNamespace = (string)$node->name;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $this->currentClass = $node->name;
        }

        switch (true) {

            case $node instanceof \PHPParser_Node_Stmt_Function:
                $this->foundFunctionNode(
                        $this->functionNodeSignatureHash($node),
                        $node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $this->foundFunctionNode($this->methodNodeSignatureHash($node), $node);
                break;

            case $node instanceof \PHPParser_Node_Expr_Closure:
                $this->foundFunctionNode(
                        $this->closureNodeSignatureHash($node),
                        $node);
                break;

            default:
                break;
        }
    }

    /**
     * @param string $hash
     */
    private function foundFunctionNode($hash, \PHPParser_Node $node)
    {
        if (!isset($this->functionNodes[$hash])) {
            $this->functionNodes[$hash] = [];
        }

        $this->functionNodes[$hash][] = $node;
    }
}
