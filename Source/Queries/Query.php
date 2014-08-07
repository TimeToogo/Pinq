<?php

namespace Pinq\Queries;

/**
 * Base implementation for the IQuery
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Query implements IQuery
{
    /**
     * @var IScope
     */
    protected $scope;

    /**
     * @var IParameterRegistry
     */
    protected $parameters;

    public function __construct(IScope $scope, IParameterRegistry $parameters)
    {
        $this->scope      = $scope;
        $this->parameters = $parameters;
    }

    final public function getScope()
    {
        return $this->scope;
    }

    final public function getParameters()
    {
        return $this->parameters;
    }
}
