<?php

namespace Pinq\Parsing\PHPParser;

use Pinq\Parsing\ParserBase;

/**
 * Function parser implementation utilising nikic\PHP-Parser to
 * accuratly locate and convert functions into the equivalent
 * expression tree
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Parser extends ParserBase
{
    /**
     * The PHP-Parser parser instanse, static because it is expensive
     * to instantiate.
     *
     * @var \PHPParser_Parser
     */
    private static $phpParser;

    /**
     * The array containing the parsed files, indexed by the file name,
     * each value contains an array of arrays of function nodes, they are grouped
     * by there location/signature hash
     *
     * @var \PHPParser_Node[][][]
     */
    private static $parsedFileFunctionNodesMap;

    public function __construct()
    {

    }

    public function getSignatureHash(\ReflectionFunctionAbstract $reflection)
    {
        return Visitors\FunctionFinderVisitor::functionSignatureHash($reflection);
    }

    protected function parseFunction(\ReflectionFunctionAbstract $reflection, $fileName)
    {
        if (self::$phpParser === null) {
            self::$phpParser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }

        $functionNodesMap = $this->getFileFunctionNodesMap($fileName);
        $functionBodyNodes = $this->getFunctionBodyNodes($functionNodesMap, $reflection);

        return (new AST($functionBodyNodes))->getExpressions();
    }

    private function getFileFunctionNodesMap($fileName)
    {
        if (!isset(self::$parsedFileFunctionNodesMap[$fileName])) {
            $parsedNodes = self::$phpParser->parse(file_get_contents($fileName));
            $functionLocatorTraverser = new \PHPParser_NodeTraverser();
            $namespaceResolver = new \PHPParser_NodeVisitor_NameResolver();
            $functionFinder = new Visitors\FunctionFinderVisitor();
            $functionLocatorTraverser->addVisitor($namespaceResolver);
            $functionLocatorTraverser->addVisitor($functionFinder);
            $functionLocatorTraverser->traverse($parsedNodes);
            self::$parsedFileFunctionNodesMap[$fileName] = $functionFinder->getLocatedFunctionNodesMap();
        }

        return self::$parsedFileFunctionNodesMap[$fileName];
    }

    private function getFunctionBodyNodes(array $functionNodesMap, \ReflectionFunctionAbstract $reflection)
    {
        $functionHash = Visitors\FunctionFinderVisitor::functionSignatureHash($reflection);

        if (!isset($functionNodesMap[$functionHash])) {
            throw \Pinq\Parsing\InvalidFunctionException::invalidFunctionMessage(
                    'Cannot parse function, the function could not be located',
                    $reflection);
        } elseif (count($functionNodesMap[$functionHash]) > 1) {
            throw \Pinq\Parsing\InvalidFunctionException::invalidFunctionMessage(
                    'Cannot parse function, two ambiguous functions are defined on the same line',
                    $reflection);
        }

        /* @var $FunctionNode PHPParser_Node_Stmt_Function|PHPParser_Node_Stmt_ClassMethod|PHPParser_Node_Expr_Closure */
        $functionNode = $functionNodesMap[$functionHash][0];

        return $functionNode->stmts;
    }
}
