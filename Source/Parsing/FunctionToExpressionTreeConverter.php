<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;
use Pinq\Caching\Provider;
use Pinq\Caching\IFunctionCache;

/**
 * Default implementaion for the IFunctionToExpressionTreeConverter.
 * Requires a parser and optionally cache which will fallback to the cache provider implementation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionToExpressionTreeConverter implements IFunctionToExpressionTreeConverter
{
    /**
     * @var IParser
     */
    protected $parser;

    /**
     * @var IFunctionCache
     */
    private $cache;

    public function __construct(IParser $parser, IFunctionCache $cache = null)
    {
        $this->parser = $parser;
        $this->cache = $cache ?: Provider::getCache();
    }

    final public function getParser()
    {
        return $this->parser;
    }

    private function getFunctionHash(\ReflectionFunctionAbstract $reflection)
    {
        return md5($this->parser->getSignatureHash($reflection) . '-' . $reflection->getFileName());
    }

    /**
     * @return \Pinq\FunctionExpressionTree
     */
    final public function convert(callable $function)
    {
        if ($function instanceof \Pinq\FunctionExpressionTree) {
            return $function;
        }

        $reflection = Reflection::fromCallable($function);
        $fullFunctionHash = $this->getFunctionHash($reflection);
        $expressionTree = $this->cache->tryGet($fullFunctionHash);

        if (!$expressionTree instanceof \Pinq\FunctionExpressionTree) {
            $expressionTree = $this->getFunctionExpressionTree($reflection);
            /*
             * Resolve all that can be currently resolved and save the expression tree with all
             * the unresolvable variables so it can be resolved with different values later
             */
            $expressionTree->simplify();
            //Force code to compile for optimal cached state
            $expressionTree->getCompiledCode();
            $this->cache->save($fullFunctionHash, $expressionTree);
        }

        //Set the compiled function with the original to prevent having to eval
        $expressionTree->setCompiledFunction($function);

        if ($expressionTree->hasUnresolvedVariables()) {
            /*
             * Simplify and resolve any remaining expressions that could not be resolved due
             * to unresolved variables
             */
            $this->resolve(
                    $expressionTree,
                    $this->getKnownVariables($reflection, $function),
                    []);
        }

        return $expressionTree;
    }

    protected function getKnownVariables(\ReflectionFunctionAbstract $reflection, callable $function)
    {
        //ReflectionFunction::getStaticVariables() returns the used variables for closures
        $knownVariables = $reflection->getStaticVariables();

        //HHVM Compatibility: hhvm does not support ReflectionFunctionAbstract::getClosureThis
        if ($function instanceof \Closure && !defined('HHVM_VERSION')) {
            $thisValue = $reflection->getClosureThis();

            if ($thisValue !== null) {
                $knownVariables['this'] = $thisValue;
            }
        } elseif (is_array($function) && is_object($function[0])) {
            $knownVariables['this'] = $function[0];
        }

        return $knownVariables;
    }

    final protected function resolve(\Pinq\FunctionExpressionTree $expressionTree, array $variableValueMap, array $variableExpressionMap)
    {
        $expressionTree->resolveVariables(
                $variableValueMap,
                $variableExpressionMap);
        $expressionTree->simplify();
    }

    /**
     * @return \Pinq\FunctionExpressionTree
     */
    final protected function getFunctionExpressionTree(\ReflectionFunctionAbstract $reflection, callable $function = null)
    {
        $parameterExpressions = $this->getParameterExpressions($reflection);
        $bodyExpressions = $reflection->isUserDefined() ? $this->parser->parse($reflection) : $this->internalFunctionExpressions($reflection);

        return new \Pinq\FunctionExpressionTree(
                $function,
                $parameterExpressions,
                $bodyExpressions);
    }

    private function internalFunctionExpressions(\ReflectionFunctionAbstract $reflection)
    {
        $hasUnavailableDefaultValueOrIsVariadic = false;
        $argumentExpressions = [];

        foreach ($reflection->getParameters() as $parameter) {
            if (($parameter->isOptional() && !$parameter->isDefaultValueAvailable())
                   || $parameter->getName() === '...') {
                $hasUnavailableDefaultValueOrIsVariadic = true;
                break;
            }

            $argumentExpressions[] = O\Expression::variable(O\Expression::value($parameter->getName()));
        }

        if (!$hasUnavailableDefaultValueOrIsVariadic) {
            return [O\Expression::returnExpression(O\Expression::functionCall(
                    O\Expression::value($reflection->getName()),
                    $argumentExpressions))];
        } else {
            return [O\Expression::returnExpression(O\Expression::functionCall(
                    O\Expression::value('call_user_func_array'),
                    [O\Expression::value($reflection->getName()), O\Expression::functionCall(O\Expression::value('func_get_args'))]))];
        }
    }

    final protected function getParameterExpressions(\ReflectionFunctionAbstract $reflection)
    {
        $parameterExpressions = [];

        foreach ($reflection->getParameters() as $parameter) {
            //Ignore variadic parameter
            if($parameter->getName() !== '...') {
                $parameterExpressions[] = $this->getParameterExpression($parameter);
            }
        }

        return $parameterExpressions;
    }

    private function getParameterExpression(\ReflectionParameter $parameter)
    {
        $typeHint = null;

        if ($parameter->isArray()) {
            $typeHint = 'array';
        } elseif ($parameter->isCallable()) {
            $typeHint = 'callable';
        } elseif ($parameter->getClass() !== null) {
            $typeHint = $parameter->getClass()->getName();
        }

        return O\Expression::parameter(
                $parameter->getName(),
                $typeHint,
                $parameter->isOptional(),
                $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                $parameter->isPassedByReference());
    }
}
