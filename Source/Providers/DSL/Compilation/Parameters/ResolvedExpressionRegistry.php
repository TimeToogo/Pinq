<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\IResolvedParameterRegistry;
use Pinq\Queries\ResolvedParameterRegistry;

/**
 * Implementation of the resolved expression parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResolvedExpressionRegistry extends ExpressionCollectionBase
{
    /**
     * @var IResolvedParameterRegistry
     */
    protected $resolvedParameters;

    /**
     * @var \SplObjectStorage
     */
    protected $resolvedValues;

    /**
     * @var mixed[]
     */
    protected $resolvedValuesArray = [];

    public function __construct(\SplObjectStorage $expressions, IResolvedParameterRegistry $resolvedParameters)
    {
        parent::__construct($expressions);
        $this->resolvedParameters = $resolvedParameters;
        $this->resolvedValues = new \SplObjectStorage();
        foreach ($this->expressions as $expression) {
            $value = $this->expressions[$expression]->evaluate($this->resolvedParameters);
            $this->resolvedValues[$expression] = $value;
            $this->resolvedValuesArray[] = $value;
        }
    }

    public static function none()
    {
        return new self(new \SplObjectStorage(), ResolvedParameterRegistry::none());
    }

    /**
     * @return IResolvedParameterRegistry
     */
    public function getResolvedParameters()
    {
        return $this->resolvedParameters;
    }

    /**
     * Gets all the resolved value of the expression.
     *
     * @return mixed[]
     */
    public function asArray()
    {
        return $this->resolvedValuesArray;
    }

    /**
     * Gets the evaluated value of supplied the expression.
     *
     * @param O\Expression $expression
     *
     * @return mixed
     */
    public function evaluate(O\Expression $expression)
    {
        return $this->resolvedValues[$expression];
    }
}