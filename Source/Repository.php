<?php 

namespace Pinq;

use Pinq\Queries;
use Pinq\Queries\Operations;

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
    protected $provider;
    
    public function __construct(Providers\IRepositoryProvider $provider, Queries\IScope $scope = null)
    {
        parent::__construct($provider, $scope);
    }
    
    public function asRepository()
    {
        return $this;
    }
    
    /**
     * Executes the supplied operation query on the underlying repository provider
     * 
     * @param Queries\IOperation $operation The operation query to execute
     * @return voide
     */
    private function executeQuery(Queries\IOperation $operation)
    {
        $this->provider->execute(new Queries\OperationQuery($this->scope, $operation));
    }
    
    public function addRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }
        
        $this->executeQuery(new Operations\AddValues($values));
        $this->valuesIterator = null;
    }
    
    public function apply(callable $function)
    {
        $this->executeQuery(new Operations\Apply($this->convert($function)));
        $this->valuesIterator = null;
    }
    
    public function removeRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }
        
        $this->executeQuery(new Operations\RemoveValues($values));
        $this->valuesIterator = null;
    }
    
    public function removeWhere(callable $predicate)
    {
        $this->executeQuery(new Operations\RemoveWhere($this->convert($predicate)));
        $this->valuesIterator = null;
    }
    
    public function clear()
    {
        $this->executeQuery(new Operations\Clear());
        $this->valuesIterator = null;
    }
    
    public function offsetSet($index, $value)
    {
        $this->executeQuery(new Operations\SetIndex($index, $value));
        $this->valuesIterator = null;
    }
    
    public function offsetUnset($index)
    {
        $this->executeQuery(new Operations\UnsetIndex($index));
        $this->valuesIterator = null;
    }
}