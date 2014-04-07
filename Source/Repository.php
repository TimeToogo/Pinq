<?php

namespace Pinq;

use \Pinq\Queries;
use \Pinq\Queries\Operations;

class Repository extends Queryable implements IRepository
{
    /**
     * @var Providers\IRepositoryProvider 
     */
    protected $Provider;
    
    public function __construct(Providers\IRepositoryProvider $Provider, Queries\IScope $Scope = null)
    {
        parent::__construct($Provider, $Scope);
    }
    
    public function AsRepository()
    {
        return $this;
    }
    
    private function ExecuteQuery(Queries\IOperation $Operation) 
    {
        $this->Provider->Execute(new Queries\OperationQuery($this->Scope, $Operation));
    }
    
    public function AddRange($Values)
    {
        $this->ExecuteQuery(new Operations\AddValues($Values));
        $this->ValuesIterator = null;
    }
    
    public function Apply(callable $Function)
    {
        $this->ExecuteQuery(new Operations\Apply($this->Convert($Function)));
        $this->ValuesIterator = null;
    }

    public function RemoveRange($Values)
    {
        $this->ExecuteQuery(new Operations\RemoveValues($Values));
        $this->ValuesIterator = null;
    }

    public function RemoveWhere(callable $Predicate)
    {
        $this->ExecuteQuery(new Operations\RemoveWhere($this->Convert($Predicate)));
        $this->ValuesIterator = null;
    }

    public function Clear()
    {
        $this->ExecuteQuery(new Operations\Clear());
        $this->ValuesIterator = null;
    }

    public function offsetSet($Index, $Value)
    {
        $this->ExecuteQuery(new Operations\SetIndex($Index, $Value));
        $this->ValuesIterator = null;
    }

    public function offsetUnset($Index)
    {
        $this->ExecuteQuery(new Operations\UnsetIndex($Index));
        $this->ValuesIterator = null;
    }

}
