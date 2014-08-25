<?php

namespace Pinq\Queries;

/**
 * Implementation of the IOperationQuery
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationQuery extends Query implements IOperationQuery
{
    /**
     * @var IOperation
     */
    private $operation;

    public function __construct(IScope $scope, IOperation $operation)
    {
        parent::__construct($scope, $operation->getParameters());
        $this->operation = $operation;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function update(IScope $scope, IOperation $operation)
    {
        if ($this->scope === $scope && $this->operation === $operation) {
            return $this;
        }

        return new self($scope, $operation);
    }

    public function updateOperation(IOperation $operation)
    {
        return $this->update($this->scope, $operation);
    }

    protected function withScope(IScope $scope)
    {
        return $this->update($scope, $this->operation);
    }
}
