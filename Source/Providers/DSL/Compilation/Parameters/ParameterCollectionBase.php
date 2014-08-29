<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;

/**
 * Base class of the expression collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ParameterCollectionBase implements \Countable
{
    /**
     * @var IQueryParameter[]
     */
    protected $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function count()
    {
        return count($this->parameters);
    }

    /**
     * @return IQueryParameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}