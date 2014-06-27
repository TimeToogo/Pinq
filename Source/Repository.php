<?php

namespace Pinq;

use Pinq\Queries;
use Pinq\Queries\Operations;
use Pinq\Iterators\IIteratorScheme;

/**
 * The standard repository class, fully implements the repository API
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Repository extends Queryable implements IRepository, Interfaces\IOrderedRepository
{
    /**
     * The repository provider for the current instance
     *
     * @var Providers\IRepositoryProvider
     */
    protected $provider;

    public function __construct(Providers\IRepositoryProvider $provider, Queries\IScope $scope = null, IIteratorScheme $scheme = null)
    {
        parent::__construct($provider, $scope, $scheme);
    }

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    protected function newSegment(Queries\ISegment $segment)
    {
        return $this->provider->createRepository($this->scope->append($segment));
    }

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    protected function updateLastSegment(Queries\ISegment $segment)
    {
        return $this->provider->createRepository($this->scope->updateLast($segment));
    }
    
    public function getSource()
    {
        return $this->isSource() ? $this : $this->provider->createRepository();
    }

    public function join($values)
    {
        return new Connectors\JoiningRepository($this->provider, $this->scope, $values, false);
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningRepository($this->provider, $this->scope, $values, true);
    }
    

    /**
     * Executes the supplied operation query on the underlying repository provider
     *
     * @param Queries\IOperation $operation The operation query to execute
     * @return void
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
        $this->elements = null;
    }

    public function apply(callable $function)
    {
        $this->executeQuery(new Operations\Apply($this->convert($function)));
        $this->elements = null;
    }
    
    public function remove($value)
    {
        $this->executeQuery(new Operations\RemoveValues([$value]));
        $this->elements = null;
    }

    public function removeRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }

        $this->executeQuery(new Operations\RemoveValues($values));
        $this->elements = null;
    }

    public function removeWhere(callable $predicate)
    {
        $this->executeQuery(new Operations\RemoveWhere($this->convert($predicate)));
        $this->elements = null;
    }

    public function clear()
    {
        $this->executeQuery(new Operations\Clear());
        $this->elements = null;
    }

    public function offsetSet($index, $value)
    {
        $this->executeQuery(new Operations\SetIndex($index, $value));
        $this->elements = null;
    }

    public function offsetUnset($index)
    {
        $this->executeQuery(new Operations\UnsetIndex($index));
        $this->elements = null;
    }
}
