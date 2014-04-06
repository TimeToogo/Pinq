<?php

namespace Pinq\Queries;

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
