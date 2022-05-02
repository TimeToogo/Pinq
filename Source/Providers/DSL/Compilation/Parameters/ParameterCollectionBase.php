<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

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

    public function count(): int
    {
        return count($this->parameters);
    }

    /**
     * Returns whether the collection contains the supplied parameter.
     *
     * @param IQueryParameter $parameter
     *
     * @return bool
     */
    public function contains(IQueryParameter $parameter)
    {
        return in_array($parameter, $this->parameters, true);
    }

    /**
     * @return IQueryParameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
