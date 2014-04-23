<?php 

namespace Pinq\Queries;

/**
 * Implementation of the IOperationQuery
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OperationQuery extends Query implements IOperationQuery
{
    /**
     * @var IOperation
     */
    private $operation;
    
    public function __construct(IScope $scope, IOperation $operation)
    {
        parent::__construct($scope);
        $this->operation = $operation;
    }
    
    public function getOperation()
    {
        return $this->operation;
    }
}