<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Queries;

/**
 * Implementation of the resolved expression parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResolvedParameterRegistry extends ParameterCollectionBase
{
    /**
     * @var Queries\IResolvedParameterRegistry
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

    /**
     * @var string[]
     */
    protected $hashes = [];

    public function __construct(array $parameters, Queries\IResolvedParameterRegistry $resolvedParameters)
    {
        parent::__construct($parameters);
        $this->resolvedParameters = $resolvedParameters;
        $this->resolvedValues = new \SplObjectStorage();
        foreach ($this->parameters as $parameter) {
            $value = $parameter->evaluate($this->resolvedParameters, $hash);
            $this->resolvedValues[$parameter] = $value;
            $this->resolvedValuesArray[] = $value;
            $this->hashes[] = $hash;
        }
    }

    public static function none()
    {
        return new self([], Queries\ResolvedParameterRegistry::none());
    }

    /**
     * @return Queries\IResolvedParameterRegistry
     */
    public function getResolvedParameters()
    {
        return $this->resolvedParameters;
    }

    /**
     * Gets an array of all the hashes of the resolved parameters.
     *
     * @return string[]
     */
    public function getHashesAsArray()
    {
        return $this->hashes;
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
     * Gets the evaluated value of supplied the parameter.
     *
     * @param IQueryParameter $parameter
     *
     * @return mixed
     */
    public function getValue(IQueryParameter $parameter)
    {
        return $this->resolvedValues[$parameter];
    }
}
