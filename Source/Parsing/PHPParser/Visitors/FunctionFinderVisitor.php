<?php

namespace Pinq\Parsing\PHPParser\Visitors;

/**
 * Attempts to locate a function node from the supplied function reflection.
 * If two ambigous function are found, it throws an exception.
 */
class FunctionFinderVisitor extends \PHPParser_NodeVisitorAbstract
{
    private static $Function = 'FUNCTION';
    private static $Method = 'METHOD';
    private static $Closure = 'CLOSURE';
    
    /**
     * The function nodes grouped by their function signature
     * 
     * @var \PHPParser_Node[][]
     */
    private $FunctionNodes = [];
    
    private $CurrentNamespace;
    private $CurrentClass;    
    
    public function beforeTraverse(array $Nodes)
    {
        $this->FunctionNodes = [];
        $this->CurrentNamespace = null;
        $this->CurrentClass = null;
    }
    
    /**
     * @return \PHPParser_Node[][]
     */
    public function GetLocatedFunctionNodesMap()
    {
        return $this->FunctionNodes;
    }
        
    private static function Hash(array $Data) 
    {
        return implode('-', $Data);
    }
    
    public static function FunctionSignatureHash(\ReflectionFunctionAbstract $Reflection) 
    {
        if($Reflection instanceof \ReflectionMethod) {
            return self::Hash([
                    self::$Method, 
                    $Reflection->getStartLine(), 
                    $Reflection->getEndLine(),  
                    self::HashParameters($Reflection),
                    $Reflection->getDeclaringClass()->getName(),
                    $Reflection->getName()
            ]);
        }
        else {
            return self::Hash([
                    $Reflection->isClosure() ? self::$Closure : self::$Function, 
                    $Reflection->getStartLine(), 
                    $Reflection->getEndLine(),
                    self::HashParameters($Reflection),
                    $Reflection->getName()
            ]);
        }
    }
    
    private static function HashParameters(\ReflectionFunctionAbstract $Reflection)
    {
        $Hash = '';
        /* @var $Parameter \ReflectionParameter */
        foreach($Reflection->getParameters() as $Parameter) {
            $Hash .= $Parameter->getName();
            $Hash .= $Parameter->isPassedByReference() ? '&' : '';
            $Hash .= $Parameter->isOptional();
        }
        
        return $Hash;
    }
    
    private function FunctionNodeSignatureHash(\PHPParser_Node_Stmt_Function $Node) 
    {
        return self::Hash([
                self::$Function, 
                $Node->getAttribute('startLine'), 
                $Node->getAttribute('endLine'), 
                $this->HashParamerNodes($Node->params),
                $this->CurrentNamespace . '\\' . $Node->name
        ]);
    }
    
    
    private function MethodNodeSignatureHash(\PHPParser_Node_Stmt_ClassMethod $Node) 
    {
        return self::Hash([
                self::$Method, 
                $Node->getAttribute('startLine'), 
                $Node->getAttribute('endLine'), 
                $this->HashParamerNodes($Node->params),
                $this->CurrentNamespace . '\\' . $this->CurrentClass,
                $Node->name
        ]);
    }
    
    private function ClosureNodeSignatureHash(\PHPParser_Node_Expr_Closure $Node)
    {
        //HHVM Compatibility: hhvm does not return namespace for closure name
        if(!defined('HHVM_VERSION')) {
            $Name = $this->CurrentNamespace . '\\{closure}';
        }
        else {
            $Name = '{closure}';
        }
        
        return self::Hash([
                self::$Closure, 
                $Node->getAttribute('startLine'), 
                $Node->getAttribute('endLine'), 
                $this->HashParamerNodes($Node->params),
                $Name
        ]);
    }
    
    private function HashParamerNodes(array $Nodes) 
    {
        $Hash = '';
        /* @var $Parameter \PHPParser_Node_Param */
        foreach($Nodes as $Parameter) {
            $Hash .= $Parameter->name;
            $Hash .= $Parameter->byRef ? '&' : '';
            $Hash .= $Parameter->default !== null;
        }
        
        return $Hash;
    }

    public function enterNode(\PHPParser_Node $Node)
    {
        if ($Node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->CurrentNamespace = (string)$Node->name;
        }
        if ($Node instanceof \PHPParser_Node_Stmt_Class) {
            $this->CurrentClass = $Node->name;
        }
        
        switch (true) {

            case $Node instanceof \PHPParser_Node_Stmt_Function:
                $this->FoundFunctionNode($this->FunctionNodeSignatureHash($Node), $Node);
                break;
            
            case $Node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $this->FoundFunctionNode($this->MethodNodeSignatureHash($Node), $Node);
                break;
            
            case $Node instanceof \PHPParser_Node_Expr_Closure:
                $this->FoundFunctionNode($this->ClosureNodeSignatureHash($Node), $Node);
                break;

            default:
                break;
        }
    }
    
    private function FoundFunctionNode($Hash, \PHPParser_Node $Node)
    {
        if (!isset($this->FunctionNodes[$Hash])) {
            $this->FunctionNodes[$Hash] = [];
        }
        
        $this->FunctionNodes[$Hash][] = $Node;
    }
}
