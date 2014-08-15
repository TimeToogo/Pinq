<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Implementation of the parameter collection context.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterCollectionContext
{
    /**
     * @var FunctionBase
     */
    protected $contextFunction;

    /**
     * @var ParameterCollection
     */
    protected $parameterCollection;

    public function __construct(FunctionBase $contextFunction, ParameterCollection $parameterCollection)
    {
        $this->contextFunction     = $contextFunction;
        $this->parameterCollection = $parameterCollection;
    }

    /**
     * Adds an expression parameter to the collection.
     *
     * @param string       $name
     * @param O\Expression $expression
     *
     * @return void
     */
    public function addExpression($name, O\Expression $expression)
    {
        $this->parameterCollection->addExpression($name, $expression, $this->contextFunction);
    }
}