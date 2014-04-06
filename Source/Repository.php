<?php

namespace Pinq;

use \Pinq\Queries;
use \Pinq\Queries\Operations;

class Repository extends Queryable implements IRepository
{
    public function __construct(Providers\IRepositoryProvider $Provider, Queries\IScope $Scope = null)
    {
        parent::__construct($Provider, $Scope);
    }
    
    private function ExecuteQuery(Queries\IOperation $Operation) 
    {
        return $this->Provider->Load(new Queries\RequestQuery($this->Scope, $Operation));
    }
    
    public function AddRange($Values)
    {
        return $this->ExecuteQuery(new Operations\AddValues($Values));
    }
    
    public function Apply(callable $Function)
    {
        return $this->ExecuteQuery(new Operations\Apply($this->Convert($Function)));
    }

    public function RemoveRange($Values)
    {
        return $this->ExecuteQuery(new Operations\RemoveValues($Values));
    }

    public function RemoveWhere(callable $Predicate)
    {
        return $this->ExecuteQuery(new Operations\RemoveWhere($Values));
    }

    public function Clear()
    {
        return $this->ExecuteQuery(new Operations\Clear());
    }

    public function offsetSet($Index, $Value)
    {
        return $this->ExecuteQuery(new Operations\SetIndex($Index, $Value));
    }

    public function offsetUnset($Index)
    {
        return $this->ExecuteQuery(new Operations\UnsetIndex($Index));
    }

}
