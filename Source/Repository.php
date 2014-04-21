<?php

namespace Pinq;

use \Pinq\Queries;
use \Pinq\Queries\Operations;

/**
 * The standard repository class, fully implements the repository API
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Repository extends Queryable implements IRepository
{
    /**
     * The repository provider for the current instance
     * 
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
    
    /**
     * Executes the supplied operation query on the underlying repository provider
     * 
     * @param Queries\IOperation $Operation The operation query to execute
     * @return voide
     */
    private function ExecuteQuery(Queries\IOperation $Operation) 
    {
        $this->Provider->Execute(new Queries\OperationQuery($this->Scope, $Operation));
    }
    
    public function AddRange($Values)
    {
        if(!Utilities::IsIterable($Values)) {
            throw PinqException::InvalidIterable(__METHOD__, $Values);
        }
        
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
        if(!Utilities::IsIterable($Values)) {
            throw PinqException::InvalidIterable(__METHOD__, $Values);
        }
        
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
