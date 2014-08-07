<?php

namespace Pinq\Providers\Collection;

use Pinq\ICollection;
use Pinq\Providers\Traversable;
use Pinq\Queries;
use Pinq\Queries\Operations;

/**
 * Evaluates the operations on the supplied collection instance
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationEvaluator extends Operations\OperationVisitor
{
    /**
     * @var ICollection
     */
    private $collection;

    /**
     * @var Queries\IResolvedParameterRegistry
     */
    protected $parameters;

    protected function __construct(
            ICollection $collection,
            Queries\IResolvedParameterRegistry $parameters
    ) {
        $this->collection = $collection;
        $this->parameters = $parameters;
    }

    public static function evaluate(
            ICollection $collection,
            Queries\IOperation $operation,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $evaluator = new self($collection, $resolvedParameters);
        $evaluator->visit($operation);
    }

    public function visitApply(Operations\Apply $operation)
    {
        $this->collection->apply($this->parameters[$operation->getMutatorFunction()->getCallableId()]);
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        /* @var $joiningCollection \Pinq\Interfaces\IJoiningToCollection */
        $joiningCollection = Traversable\ScopeEvaluator::evaluateJoinOptions(
                $this->collection,
                $operation->getOptions(),
                $this->parameters
        );

        $joiningCollection->apply($this->parameters[$operation->getMutatorFunction()->getCallableId()]);
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        $this->collection->addRange(
                Traversable\ScopeEvaluator::evaluateSource(
                        $operation->getSource(),
                        $this->parameters
                )
        );
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        $this->collection->removeRange(
                Traversable\ScopeEvaluator::evaluateSource(
                        $operation->getSource(),
                        $this->parameters
                )
        );
    }

    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        $this->collection->removeWhere($this->parameters[$operation->getPredicateFunction()->getCallableId()]);
    }

    public function visitClear(Operations\Clear $operation)
    {
        $this->collection->clear();
    }

    public function visitSetIndex(Operations\SetIndex $operation)
    {
        $this->collection[$this->parameters[$operation->getIndexId()]] = $this->parameters[$operation->getValueId()];
    }

    public function visitUnsetIndex(Operations\UnsetIndex $operation)
    {
        unset($this->collection[$this->parameters[$operation->getIndexId()]]);
    }
}
