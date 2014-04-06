<?php

namespace Pinq\Queries;

abstract class Query implements IQuery
{
    /**
     * @var IScope
     */
    private $Scope;
    
    public function __construct(IScope $Scope)
    {
        $this->Scope = $Scope;
    }
    
    final public function GetScope()
    {
        return $this->Scope;
    }
}
