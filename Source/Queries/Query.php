<?php

namespace Pinq\Queries;

/**
 * Base implementation for the IQuery
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
