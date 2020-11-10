<?php

namespace Pinq\Parsing\PhpParser\Visitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Pinq\Expressions as O;
use Pinq\Parsing\FunctionDeclaration;
use Pinq\Parsing\FunctionLocation;
use Pinq\Parsing\FunctionSignature;
use Pinq\Parsing\PhpParser\AST;
use Pinq\Parsing\PhpParser\LocatedFunctionNode;

/**
 * Visits the supplied nodes and stores any located functions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionLocatorVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * The located function nodes grouped by their location hash.
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

    private function getFunctionNodeSignature(Node\Stmt\Function_ $node)
    {
        return FunctionSignature::func(
                $node->byRef,
                $node->name,
                $this->getParameterExpressions($node->params)
        );
    }

    private function getClosureNodeSignature(Node\Expr\Closure $node)
    {
        $scopedVariableNames = [];
        foreach ($node->uses as $use) {
            $scopedVariableNames[] = (string)$use->var->name;
        }

        return FunctionSignature::closure(
                $node->byRef,
                $this->getParameterExpressions($node->params),
                $scopedVariableNames
        );
    }

    private function getMethodNodeSignature(Node\Stmt\ClassMethod $node)
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

    private function getLocatedFunctionNode(Node $node, FunctionSignature $signature)
    {
        return new LocatedFunctionNode(
                $signature,
                $this->getFunctionLocation($node),
                $this->getFunctionDeclaration(),
                $node);
    }

    /**
     * @param Node\Param[] $parameters
     *
     * @return O\ParameterExpression[]
     */
    private function getParameterExpressions(array $parameters)
    {
        return AST::convert($parameters);
    }

    private function getFunctionLocation(Node $node)
    {
        return new FunctionLocation(
                $this->filePath,
                $this->namespace,
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

    public function enterNode(Node $node)
    {
        switch (true) {

            case $node instanceof Node\Stmt\Namespace_:
                $this->namespace = (string) $node->name;
                break;

            case $node instanceof Node\Stmt\Class_:
                $this->class = $node->name;
                break;

            case $node instanceof Node\Stmt\Trait_:
                $this->trait = $node->name;
                break;

            case $node instanceof Node\Stmt\Function_:
                $signature = $this->getFunctionNodeSignature($node);
                $this->foundFunctionNode($this->getLocatedFunctionNode($node, $signature));
                $this->function = $node->name;
                break;

            case $node instanceof Node\Stmt\ClassMethod:
                $signature = $this->getMethodNodeSignature($node);
                $this->foundFunctionNode($this->getLocatedFunctionNode($node, $signature));
                $this->function = $node->name;
                break;

            case $node instanceof Node\Expr\Closure:
                $signature = $this->getClosureNodeSignature($node);
                $this->foundFunctionNode($this->getLocatedFunctionNode($node, $signature));
                $this->closureNestingLevel++;
                break;

            default:
                break;
        }
    }

    public function leaveNode(Node $node)
    {
        switch (true) {

            case $node instanceof Node\Stmt\Namespace_:
                $this->namespace = null;
                break;

            case $node instanceof Node\Stmt\Class_:
                $this->class = null;
                break;

            case $node instanceof Node\Stmt\Trait_:
                $this->trait = null;
                break;

            case $node instanceof Node\Stmt\Function_:
            case $node instanceof Node\Stmt\ClassMethod:
                $this->function = null;
                break;

            case $node instanceof Node\Expr\Closure:
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
