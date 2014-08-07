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

    public function __construct(IScope $scope, IOperation $operation, IParameterRegistry $parameters)
    {
        parent::__construct($scope, $parameters);
        $this->operation = $operation;
    }

    public function getOperation()
    {
        return $this->operation;
    }
}
