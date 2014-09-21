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
     * @var Parameters\ParameterRegistry
     */
    protected $parameters;

    public function __construct(Parameters\ParameterRegistry $parameters)
    {
        $this->parameters = $parameters;
    }

    final public function getParameterRegistry()
    {
        return $this->parameters;
    }
}
