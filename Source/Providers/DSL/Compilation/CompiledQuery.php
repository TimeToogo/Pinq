<?php

namespace Pinq\Providers\DSL\Compilation;

/**
 * Base class of a compiled query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CompiledQuery implements ICompiledQuery
{
    /**
     * @var Parameters\ExpressionRegistry
     */
    protected $parameters;

    public function __construct(Parameters\ExpressionRegistry $parameters)
    {
        $this->parameters = $parameters;
    }

    final public function getParameterRegistry()
    {
        return $this->parameters;
    }
}