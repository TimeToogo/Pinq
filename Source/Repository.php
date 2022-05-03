<?php

namespace Pinq;

use Pinq\Expressions as O;
use Pinq\Iterators\IIteratorScheme;

/**
 * The standard repository class, fully implements the repository API
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Repository extends Queryable implements IRepository, Interfaces\IOrderedRepository
{
    /**
     * The repository provider for the current instance
     *
     * @var Providers\IRepositoryProvider
     */
    protected $repositoryProvider;

    public function __construct(
            Providers\IRepositoryProvider $provider,
            Queries\ISourceInfo $sourceInfo,
            O\TraversalExpression $queryExpression = null,
            IIteratorScheme $scheme = null
    ) {
        parent::__construct($provider, $sourceInfo, $queryExpression, $scheme);

        $this->repositoryProvider = $provider;
    }

    /**
     * Executes the supplied operation query expression on the underlying repository provider.
     *
     * @param O\Expression $expression
     *
     * @return void
     */
    private function executeQuery(O\Expression $expression)
    {
        $this->repositoryProvider->execute($expression);
    }

    /**
     * {@inheritDoc}
     * @return IRepository
     */
    protected function newMethodSegment($name, array $arguments = [])
    {
        return $this->repositoryProvider->createRepository($this->newMethod($name, $arguments));
    }

    public function join($values)
    {
        return new Connectors\JoiningRepository(
                $this->repositoryProvider,
                $this->newMethod(__FUNCTION__, [$values]));
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningRepository(
                $this->repositoryProvider,
                $this->newMethod(__FUNCTION__, [$values]));
    }

    public function addRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }

        $this->executeQuery($this->newMethod(__FUNCTION__, [$values]));
    }

    public function apply(callable $function)
    {
        $this->executeQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function remove($value)
    {
        $this->executeQuery($this->newMethod(__FUNCTION__, [$value]));
    }

    public function removeRange($values)
    {
        if (!Utilities::isIterable($values)) {
            throw PinqException::invalidIterable(__METHOD__, $values);
        }

        $this->executeQuery($this->newMethod(__FUNCTION__, [$values]));
    }

    public function removeWhere(callable $predicate)
    {
        $this->executeQuery($this->newMethod(__FUNCTION__, [$predicate]));
    }

    public function clear()
    {
        $this->executeQuery($this->newMethod(__FUNCTION__));
    }

    public function offsetSet($index, $value): void
    {
        $this->executeQuery($this->newMethod(__FUNCTION__, [$index, $value]));
    }

    public function offsetUnset($index): void
    {
        $this->executeQuery($this->newMethod(__FUNCTION__, [$index]));
    }
}
