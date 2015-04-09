<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\Functions\IFunction;

/**
 * Implementation of the expression parameter collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterCollection extends ParameterCollectionBase
{
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * Adds an expression parameter to the collection.
     *
     * @param O\Expression     $expression
     * @param IParameterHasher $hasher
     * @param IFunction|null   $context
     * @param mixed            $data
     *
     * @return void
     */
    public function addExpression(
            O\Expression $expression,
            IParameterHasher $hasher,
            IFunction $context = null,
            $data = null
    ) {
        $this->parameters[] = new ExpressionParameter($expression, $hasher, $context, $data);
    }

    /**
     * Adds a standard parameter id to the collection.
     *
     * @param                  $parameterId
     * @param IParameterHasher $hasher
     * @param mixed            $data
     *
     * @return void
     */
    public function addId($parameterId, IParameterHasher $hasher, $data = null)
    {
        $this->parameters[] = new StandardParameter($parameterId, $hasher, $data);
    }

    /**
     * Adds an parameter to the collection with the supplied context.
     *
     * @param IQueryParameter $parameter
     *
     * @return void
     */
    public function add(IQueryParameter $parameter)
    {
        $this->parameters[] = $parameter;
    }

    /**
     * Removes a query parameter from the collection.
     *
     * @param IQueryParameter $parameter
     *
     * @return void
     */
    public function remove(IQueryParameter $parameter)
    {
        foreach($this->parameters as $key => $someParameter) {
            if($someParameter === $parameter) {
                unset($this->parameters[$key]);
            }
        }
    }

    /**
     * Builds an immutable parameter registry from the added parameters.
     *
     * @return ParameterRegistry
     */
    public function buildRegistry()
    {
        return new ParameterRegistry($this->parameters);
    }
}
