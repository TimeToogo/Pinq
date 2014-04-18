<?php

namespace Pinq\Parsing\PHPParser;

use \Pinq\Parsing\ParserBase;

class Parser extends ParserBase
{
    /**
     * @var \PHPParser_Parser 
     */
    private static $PHPParser;
        
    /**
     * @var \PHPParser_Node[][][] 
     */
    private static $ParsedFileFunctionNodesMap;

    public function __construct()
    {
    }
    
    public function GetSignatureHash(\ReflectionFunctionAbstract $Reflection)
    {
        return Visitors\FunctionFinderVisitor::FunctionSignatureHash($Reflection);
    }

    protected function ParseFunction(\ReflectionFunctionAbstract $Reflection, $FileName)
    {
        if (self::$PHPParser === null) {
            self::$PHPParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }

        $FunctionNodesMap = $this->GetFileFunctionNodesMap($FileName);
        $FunctionBodyNodes = $this->GetFunctionBodyNodes($FunctionNodesMap, $Reflection);

        return (new AST($FunctionBodyNodes))->GetExpressions();
    }

    private function GetFileFunctionNodesMap($FileName)
    {
        if (!isset(self::$ParsedFileFunctionNodesMap[$FileName])) {
            $ParsedNodes =  self::$PHPParser->parse(file_get_contents($FileName));
            
            $FunctionLocatorTraverser = new \PHPParser_NodeTraverser();

            $NamespaceResolver = new \PHPParser_NodeVisitor_NameResolver();
            $FunctionFinder = new Visitors\FunctionFinderVisitor();
            $FunctionLocatorTraverser->addVisitor($NamespaceResolver);
            $FunctionLocatorTraverser->addVisitor($FunctionFinder);
            
            $FunctionLocatorTraverser->traverse($ParsedNodes);
            
            
            self::$ParsedFileFunctionNodesMap[$FileName] = $FunctionFinder->GetLocatedFunctionNodesMap();
        }

        return self::$ParsedFileFunctionNodesMap[$FileName];
    }

    private function GetFunctionBodyNodes(array $FunctionNodesMap, \ReflectionFunctionAbstract $Reflection)
    {
        $FunctionHash = Visitors\FunctionFinderVisitor::FunctionSignatureHash($Reflection);
        
        if (!isset($FunctionNodesMap[$FunctionHash])) {
            throw \Pinq\Parsing\InvalidFunctionException::InvalidFunctionMessage(
                    'Cannot parse function, the function could not be located',
                    $Reflection);
        }
        else if (count($FunctionNodesMap[$FunctionHash]) > 1) {
            throw \Pinq\Parsing\InvalidFunctionException::InvalidFunctionMessage(
                    'Cannot parse function, two ambiguous functions are defined on the same line',
                    $Reflection);
        }

        /* @var $FunctionNode PHPParser_Node_Stmt_Function|PHPParser_Node_Stmt_ClassMethod|PHPParser_Node_Expr_Closure */
        $FunctionNode = $FunctionNodesMap[$FunctionHash][0];
        
        return $FunctionNode->stmts;
    }
}
