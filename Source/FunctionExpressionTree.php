<?php

namespace Pinq;

use Pinq\Expressions as O;

/**
 * Acts as a mutable container and compiler for the underlying expression tree of a function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionExpressionTree implements \Serializable
{
    /**
     * @var O\ParameterExpression[]
     */
    private $parameterExpressions;

    /**
     * The expressions of the body statements of the function
     *
     * @var O\Expression[]
     */
    private $bodyExpressions = [];

    /**
     * The body expressions with resolved sub queries
     *
     * @var O\Expression[]
     */
    private $bodyExpressionsWithSubQueries = [];

    /**
     * Expressions which can resolve to subqueries
     *
     * @var O\Expression[]
     */
    private $queryableExpressions = [];

    /**
     * @var O\ReturnExpression[]
     */
    private $returnValueExpressions = [];

    /**
     * @var O\Walkers\VariableResolver
     */
    private $variableResolver;

    /**
     * @var O\Walkers\UnresolvedVariableFinder
     */
    private $unresolvedVariableFinder;

    /**
     * @var O\Walkers\ValueUnresolver
     */
    private $valueUnresolver;

    /**
     * @var O\Walkers\ReturnValueExpressionResolver
     */
    private $returnValueExpressionResolver;

    /**
     * @var O\Walkers\SubQueryResolver
     */
    private $subQueryResolver;

    /**
     * @var string[]
     */
    private $unresolvedVariables = [];

    /**
     * @var string|null
     */
    private $serializedData = null;

    /**
     * @var string|null
     */
    private $compiledCode = null;

    /**
     * @var callable|null
     */
    private $compiledFunction = null;

    public function __construct(callable $originalFunction = null, array $parameterExpressions, array $expressions)
    {
        $this->parameterExpressions = $parameterExpressions;
        $this->variableResolver = new O\Walkers\VariableResolver();
        $this->unresolvedVariableFinder = new O\Walkers\UnresolvedVariableFinder();
        $this->returnValueExpressionResolver = new O\Walkers\ReturnValueExpressionResolver();
        //Only parameterize objects, arrays and resources. Filter cannot be closure due to serialization.
        $this->valueUnresolver = new O\Walkers\ValueUnresolver([__CLASS__, 'isNotScalar']);
        $this->subQueryResolver = new O\Walkers\SubQueryResolver();
        $this->invalidate($expressions);
        $this->compiledFunction = $originalFunction;
    }

    public static function isNotScalar($value)
    {
        return !is_scalar($value);
    }

    public static function fromClosureExpression(O\ClosureExpression $expression, callable $originalFunction = null)
    {
        return new self(
                $originalFunction,
                $expression->getParameterExpressions(),
                $expression->getBodyExpressions());
    }

    public function setCompiledFunction(callable $function = null)
    {
        $this->compiledFunction = $function;
    }

    /**
     * @return string
     */
    public function getCompiledCode()
    {
        $this->loadCompiledFunction();

        return $this->compiledCode;
    }

    /**
     * @return callable
     */
    public function getCompiledFunction()
    {
        return $this->loadCompiledFunction();
    }

    public function serialize()
    {
        if ($this->serializedData === null) {
            $dataToSerialize = get_object_vars($this);
            unset($dataToSerialize['serializedData']);
            unset($dataToSerialize['compiledFunction']);
            $this->serializedData = serialize($dataToSerialize);
        }

        return $this->serializedData;
    }

    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $propertyName => $value) {
            $this->{$propertyName} = $value;
        }

        $this->serializedData = $serialized;
    }

    public function __clone()
    {
        foreach ($this->bodyExpressions as $key => $bodyExpression) {
            $this->bodyExpressions[$key] = clone $bodyExpression;
        }

        foreach ($this->bodyExpressionsWithSubQueries as $key => $bodyExpression) {
            $this->bodyExpressionsWithSubQueries[$key] = clone $bodyExpression;
        }

        foreach ($this->parameterExpressions as $key => $parameterExpressions) {
            $this->parameterExpressions[$key] = clone $parameterExpressions;
        }

        foreach ($this->returnValueExpressions as $key => $returnExpression) {
            $this->returnValueExpressions[$key] = clone $returnExpression;
        }

        $this->compiledFunction = $this->compiledFunction === null ? null : clone $this->compiledFunction;
        $this->returnValueExpressionResolver = clone $this->returnValueExpressionResolver;
        $this->valueUnresolver = clone $this->valueUnresolver;
        $this->variableResolver = clone $this->variableResolver;
    }

    public function __invoke()
    {
        $function = $this->loadCompiledFunction();

        return call_user_func_array($function, func_get_args());
    }

    private function loadCompiledFunction()
    {
        if ($this->compiledFunction === null) {
            $this->valueUnresolver->resetVariableNameValueMap();
            $parameterizedBodyExpressions = $this->valueUnresolver->walkAll($this->bodyExpressions);
            $parameterNameValueMap = $this->valueUnresolver->getVariableNameValueMap();

            if ($this->compiledCode === null) {
                $this->compiledCode =
                        O\Expression::closure(
                                $this->parameterExpressions,
                                array_keys($parameterNameValueMap),
                                $parameterizedBodyExpressions)->compile();
            }

            $this->evaluateFunctionCode(
                    $this->compiledCode,
                    $parameterNameValueMap);

            if (!$this->compiledFunction instanceof \Closure) {
                throw new PinqException(
                        'Could not compile code into closure: %s',
                        $this->compiledCode);
            }
        }

        return $this->compiledFunction;
    }

    /**
     * @param string $___Code____00987654321
     */
    private function evaluateFunctionCode($___Code____00987654321, array $___UsedVariableNameValueMap____1234567890)
    {
        extract($___UsedVariableNameValueMap____1234567890);
        eval('$this->compiledFunction = ' . $___Code____00987654321 . ';');
    }

    /**
     * Gets the body expressions
     *
     * @return O\Expression[]
     */
    final public function getExpressions()
    {
        return $this->bodyExpressionsWithSubQueries;
    }

    /**
     * @return boolean
     */
    final public function hasReturnExpression()
    {
        return count($this->returnValueExpressions) > 0;
    }

    /**
     * @return O\Expression
     * @throws Parsing\InvalidFunctionException
     */
    final public function getFirstResolvedReturnValueExpression()
    {
        if (count($this->returnValueExpressions) === 0) {
            throw Parsing\InvalidFunctionException::mustContainValidReturnExpression(Parsing\Reflection::fromCallable($this->getCompiledFunction()));
        }

        return $this->returnValueExpressions[0];
    }

    /**
     * @return O\ParameterExpression[]
     */
    final public function getParameterExpressions()
    {
        return $this->parameterExpressions;
    }

    final public function walk(O\ExpressionWalker $expressionWalker)
    {
        $this->invalidate($expressionWalker->walkAll($this->bodyExpressions));

        return $this;
    }

    final public function simplify()
    {
        $this->invalidate(O\Expression::simplifyAll($this->bodyExpressions));

        return $this;
    }

    final public function hasUnresolvedVariables()
    {
        return !empty($this->unresolvedVariables);
    }

    final public function getUnresolvedVariables()
    {
        return $this->unresolvedVariables;
    }

    final public function resolveVariables(array $variableValueMap, array $variableExpressionMap = [])
    {
        foreach ($variableValueMap as $variableName => $value) {
            $variableValueMap[$variableName] = O\Expression::value($value);
        }

        $this->resolveVariablesToExpressions($variableExpressionMap + $variableValueMap);
    }

    final public function resolveVariablesToExpressions(array $variableExpressionMap)
    {
        $this->variableResolver->setVariableExpressionMap($variableExpressionMap);
        $this->invalidate($this->variableResolver->walkAll($this->bodyExpressions));
    }

    final public function setQueryableExpressions(array $expressions)
    {
        $this->queryableExpressions = $expressions;
        $this->invalidate($this->bodyExpressions);
    }

    final protected function invalidate(array $newBodyExpressions)
    {
        if ($this->bodyExpressions === $newBodyExpressions) {
            return;
        }

        $this->bodyExpressions = $newBodyExpressions;
        $this->serializedData = null;
        $this->compiledCode = null;
        $this->compiledFunction = null;
        $this->loadUnresolvedVariables();
        $this->loadResolvedReturnExpressions();
        $this->loadResolvedSubQueriesExpressions();
    }

    private function loadUnresolvedVariables()
    {
        $this->unresolvedVariableFinder->resetUnresolvedVariables();
        $this->unresolvedVariableFinder->walkAll($this->bodyExpressions);
        $parameterNames = [];

        foreach ($this->parameterExpressions as $parameterExpression) {
            $parameterNames[] = $parameterExpression->getName();
        }

        $this->unresolvedVariables = array_diff($this->unresolvedVariableFinder->getUnresolvedVariables(), $parameterNames);
    }

    private function loadResolvedReturnExpressions()
    {
        $this->returnValueExpressionResolver->resetReturnExpressions();
        $this->returnValueExpressionResolver->walkAll($this->bodyExpressions);
        $this->returnValueExpressions = $this->returnValueExpressionResolver->getResolvedReturnValueExpression();
        $this->returnValueExpressions = $this->resolveSubQueries($this->returnValueExpressions);
    }

    private function loadResolvedSubQueriesExpressions()
    {
        $this->bodyExpressionsWithSubQueries = $this->resolveSubQueries($this->bodyExpressions);
    }

    private function resolveSubQueries(array $expressions)
    {
        $this->subQueryResolver->setFilter(function (O\MethodCallExpression $expression) {
            $valueExpression = $expression->getValueExpression();

            if (in_array($valueExpression, $this->queryableExpressions)) {
                return true;
            } elseif ($valueExpression instanceof O\ValueExpression && $valueExpression->getValue() instanceof ITraversable) {
                return true;
            } elseif ($valueExpression instanceof O\VariableExpression && $valueExpression->getNameExpression() instanceof O\ValueExpression) {
                $variableName = $valueExpression->getNameExpression()->getValue();

                foreach ($this->parameterExpressions as $parameterExpression) {
                    if ($parameterExpression->hasTypeHint() && is_a($parameterExpression->getTypeHint(), ITraversable::ITRAVERSABLE_TYPE, true) && $parameterExpression->getName() === $variableName) {
                        return true;
                    }
                }
            }

            return false;
        });
        $resolvedExpressions = $this->subQueryResolver->walkAll($expressions);
        $this->subQueryResolver->setFilter(null);

        return $resolvedExpressions;
    }
}
