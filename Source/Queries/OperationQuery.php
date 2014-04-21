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
    private $Operation;
    
    public function __construct(IScope $Scope, IOperation $Operation)
    {
        parent::__construct($Scope);
        $this->Operation = $Operation;
    }

    public function GetOperation()
    {
        return $this->Operation;
    }
}
