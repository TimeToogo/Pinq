<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\FunctionBase;
use Pinq\Queries\ParameterRegistry;

/**
 * Implementation of the expression parameter collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionCollection extends ExpressionCollectionBase
{
    public function __construct()
    {
        parent::__construct(new \SplObjectStorage());
    }

    /**
     * Adds an expression parameter to the collection with the supplied context.
     *
     * @param O\Expression      $expression
     * @param FunctionBase|null $context
     * @param mixed             $data
     *
     * @return void
     */
    public function add(O\Expression $expression, FunctionBase $context = null, $data = null)
    {
        $this->expressions[$expression] = new ExpressionParameter($expression, $context, $data);
    }

    /**
     * Returns a parameter collection context for the supplied function.
     *
     * @param FunctionBase $context
     *
     * @return ExpressionCollectionContext
     */
    public function forFunction(FunctionBase $context)
    {
        return new ExpressionCollectionContext($context, $this);
    }

    /**
     * Builds an immutable parameter registry from the added parameters.
     *
     * @return ExpressionRegistry
     */
    public function buildRegistry()
    {
        return new ExpressionRegistry($this->expressions);
    }
}