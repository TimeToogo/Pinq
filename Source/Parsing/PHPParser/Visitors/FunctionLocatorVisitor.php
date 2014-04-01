<?php

namespace Pinq\Parsing\PHPParser\Visitors;

/**
 * Attempts to locate a function node from the supplied function reflection.
 * If two ambigous function are found, it throws an exception.
 */
class FunctionLocatorVisitor extends \PHPParser_NodeVisitorAbstract
{
    /**
     * @var \ReflectionFunctionAbstract
     */
    private $Reflection;

    private $CurrentNamespace;
    private $NamespaceName;
    private $FunctionName;
    private $StartLine;
    private $EndLine;

    private $HasLocatedFunction = false;
    private $FunctionNode;
    private $BodyNodes;

    public function __construct(\ReflectionFunctionAbstract $Reflection)
    {
        $this->Reflection = $Reflection;
        $this->NamespaceName = $this->GetNamespace($Reflection);
        $this->FunctionName = $Reflection->getShortName();
        $this->StartLine = $Reflection->getStartLine();
        $this->EndLine = $Reflection->getEndLine();
    }

    private function GetNamespace(\ReflectionFunctionAbstract $Reflection)
    {
        return $Reflection instanceof \ReflectionMethod ? $Reflection->getDeclaringClass()->getNamespaceName() : $Reflection->getNamespaceName();
    }

    /**
     * @return boolean
     */
    public function HasLocatedFunction()
    {
        return $this->HasLocatedFunction;
    }

    /**
     * @return \PHPParser_Node
     */
    public function GetFunctionNode()
    {
        return $this->FunctionNode;
    }

    /**
     * @return \PHPParser_Node[]
     */
    public function GetBodyNodes()
    {
        return $this->BodyNodes;
    }

    private function MatchesFunctionName($FunctionName)
    {
        return $this->NamespaceName === $this->CurrentNamespace  && $this->FunctionName === $FunctionName;
    }

    public function enterNode(\PHPParser_Node $Node)
    {
        if ($Node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->CurrentNamespace = (string) $Node->name;
        }

        if($Node->getAttribute('startLine') === $this->StartLine
                && $Node->getAttribute('endLine') === $this->EndLine) {

            switch (true) {

                case $Node instanceof \PHPParser_Node_Stmt_Function && $this->MatchesFunctionName($Node->name):
                case $Node instanceof \PHPParser_Node_Stmt_ClassMethod && $this->MatchesFunctionName($Node->name):
                case $Node instanceof \PHPParser_Node_Expr_Closure && $this->MatchesFunctionName('{closure}'):
                    $this->FoundFunctionNode($Node);
                    break;

                default:
                    break;
            }
        }
    }

    private function FoundFunctionNode(\PHPParser_Node $Node)
    {
        if ($this->HasLocatedFunction) {
            throw new \Pinq\Parsing\InvalidFunctionException(
                    'Cannot parse function %s defined in %s on line %d: Two ambiguous functions are defined on one line, get your shit together',
                    $this->Reflection->getName(),
                    $this->Reflection->getFileName(),
                    $this->StartLine);
        }

        $this->FunctionNode = $Node;
        $this->BodyNodes = $Node->stmts;
        $this->HasLocatedFunction = true;
    }
}
