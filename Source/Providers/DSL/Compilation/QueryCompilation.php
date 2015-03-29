<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Providers\DSL\Compilation\Parameters\ParameterCollection;

/**
 * Base class for an request / operation query undergoing compilation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryCompilation implements IQueryCompilation
{
    /**
     * @var ParameterCollection
     */
    protected $parameters;

    public function __construct(ParameterCollection $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
