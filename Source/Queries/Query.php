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

    public function __construct(IScope $scope, array $queryParameters)
    {
        $this->scope      = $scope;
        $this->parameters = new ParameterRegistry(array_merge($scope->getParameters(), $queryParameters));
    }

    final public function getScope()
    {
        return $this->scope;
    }

    final public function getParameters()
    {
        return $this->parameters;
    }

    public function updateScope(IScope $scope)
    {
        if ($this->scope === $scope) {
            return $this;
        }

        return $this->withScope($scope);
    }

    abstract protected function withScope(IScope $scope);
}
