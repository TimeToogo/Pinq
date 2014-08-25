<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Implementation of the parameter collection context.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionCollectionContext
{
    /**
     * @var FunctionBase
     */
    protected $contextFunction;

    /**
     * @var ExpressionCollection
     */
    protected $parameterCollection;

    public function __construct(FunctionBase $contextFunction, ExpressionCollection $parameterCollection)
    {
        $this->contextFunction     = $contextFunction;
        $this->parameterCollection = $parameterCollection;
    }

    /**
     * @return FunctionBase
     */
    public function getFunction()
    {
        return $this->contextFunction;
    }

    /**
     * Adds an expression parameter to the collection.
     *
     * @param O\Expression $expression
     * @param mixed        $data
     *
     * @return void
     */
    public function add(O\Expression $expression, $data = null)
    {
        $this->parameterCollection->add($expression, $this->contextFunction, $data);
    }
}