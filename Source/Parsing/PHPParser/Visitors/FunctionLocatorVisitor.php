<?php

namespace Pinq\Parsing\PHPParser\Visitors;

use Pinq\Expressions as O;
use Pinq\Parsing\FunctionDeclaration;
use Pinq\Parsing\FunctionLocation;
use Pinq\Parsing\FunctionSignature;
use Pinq\Parsing\PHPParser\AST;
use Pinq\Parsing\PHPParser\LocatedFunctionNode;

/**
 * Visits the supplied nodes and stores any located functions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionLocatorVisitor extends \PHPParser_NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * The locatied function nodes grouped by their location hash.
     *
     * @var LocatedFunctionNode[][]
     */
    private $functionNodes = [];

    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var string|null
     */
    private $class;

    /**
     * @var string|null
     */
    private $trait;

    /**
     * @var string|null
     */
    private $function;

    /**
     * @var int
     */
    private $closureNestingLevel = 0;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return LocatedFunctionNode[][]
     */
    public function getLocatedFunctionNodesMap()
    {
        return $this->functionNodes;
    }

    private function getFunctionNodeSignature(\PHPParser_Node_Stmt_Function $node)
    {
        return FunctionSignature::func(
                $node->byRef,
                $node->name,
                $this->getParameterExpressions($node->params)
        );
    }

    private function getClosureNodeSignature(\PHPParser_Node_Expr_Closure $node)
    {
        $scopedVariableNames = [];
        foreach ($node->uses as $use) {
            $scopedVariableNames[] = $use->var;
        }

        return FunctionSignature::closure(
                $node->byRef,
                $this->getParameterExpressions($node->params),
                $scopedVariableNames
        );
    }

    private function getMethodNodeSignature(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        if ($node->isPublic()) {
            $accessModifier = FunctionSignature::ACCESS_PUBLIC;
        } elseif ($node->isProtected()) {
            $accessModifier = FunctionSignature::ACCESS_PROTECTED;
        } else {
            $accessModifier = FunctionSignature::ACCESS_PRIVATE;
        }

        if ($node->isFinal()) {
            $polymorphModifier = FunctionSignature::POLYMORPH_FINAL;
        } elseif ($node->isAbstract()) {
            $polymorphModifier = FunctionSignature::POLYMORPH_ABSTRACT;
        } else {
            $polymorphModifier = null;
        }

        return FunctionSignature::method(
                $node->byRef,
                $accessModifier,
                $polymorphModifier,
                $node->isStatic(),
                $node->name,
                $this->getParameterExpressions($node->params)
        );
    }

    private function getLocatedFunctionNode(\PHPParser_Node $node, FunctionSignature $signature)
    {
        return new LocatedFunctionNode(
                $signature,
                $this->getFunctionLocation($node),
                $this->getFunctionDeclaration(),
                $node);
    }

    /**
     * @param \PHPParser_Node_Param[] $parameters
     *
     * @return O\ParameterExpression[]
     */
    private function getParameterExpressions(array $parameters)
    {
        return AST::convert($parameters);
    }

    private function getFunctionLocation(\PHPParser_Node $node)
    {
        return new FunctionLocation(
                $this->filePath,
                $node->getAttribute('startLine'),
                $node->getAttribute('endLine'));
    }

    private function getFunctionDeclaration()
    {
        return new FunctionDeclaration(
                $this->namespace,
                $this->class,
                $this->trait,
                $this->function,
                $this->closureNestingLevel);
    }

    public function enterNode(\PHPParser_Node $node)
    {
        switch (true) {

            case $node instanceof \PHPParser_Node_Stmt_Namespace:
                $this->namespace = (string) $node->name;
                break;

            case $node instanceof \PHPParser_Node_Stmt_Class:
                $this->class = $node->name;
                break;

            case $node instanceof \PHPParser_Node_Stmt_Trait:
                $this->trait = $node->name;
                break;

            case $node instanceof \PHPParser_Node_Stmt_Function:
                $signature = $this->getFunctionNodeSignature($node);
                $this->foundFunctionNode($this->getLocatedFunctionNode($node, $signature));
                $this->function = $node->name;
                break;

            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $signature = $this->getMethodNodeSignature($node);
                $this->foundFunctionNode($this->getLocatedFunctionNode($node, $signature));
                $this->function = $node->name;
                break;

            case $node instanceof \PHPParser_Node_Expr_Closure:
                $signature = $this->getClosureNodeSignature($node);
                $this->foundFunctionNode($this->getLocatedFunctionNode($node, $signature));
                $this->closureNestingLevel++;
                break;

            default:
                break;
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch (true) {

            case $node instanceof \PHPParser_Node_Stmt_Namespace:
                $this->namespace = null;
                break;

            case $node instanceof \PHPParser_Node_Stmt_Class:
                $this->class = null;
                break;

            case $node instanceof \PHPParser_Node_Stmt_Trait:
                $this->trait = null;
                break;

            case $node instanceof \PHPParser_Node_Stmt_Function:
            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $this->function = null;
                break;

            case $node instanceof \PHPParser_Node_Expr_Closure:
                $this->closureNestingLevel--;
                break;

            default:
                break;
        }
    }

    private function foundFunctionNode(LocatedFunctionNode $locatedNode)
    {
        $locationHash = $locatedNode->getLocation()->getHash();
        if (!isset($this->functionNodes[$locationHash])) {
            $this->functionNodes[$locationHash] = [];
        }

        $this->functionNodes[$locationHash][] = $locatedNode;
    }
}
