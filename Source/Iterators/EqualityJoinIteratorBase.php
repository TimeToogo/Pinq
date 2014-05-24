<?php

namespace Pinq\Iterators;

/**
 * Base class for a join iterator with an inner function whose result must be equal
 * to that of the outer function.
 * Because of this, the inner values are organized into a Lookup where they are keyed by the
 * inner function. The outer function is then run and the resulting key is searched for in
 * the lookup to retrieve the matching values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class EqualityJoinIteratorBase extends JoinIteratorBase
{
    /**
     * @var callable
     */
    private $outerKeyFunction;

    /**
     * @var callable
     */
    private $innerKeyFunction;

    /**
     * @var Utilities\Lookup
     */
    private $innerKeyLookup;

    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $outerKeyFunction, callable $innerKeyFunction, callable $joiningFunction)
    {
        parent::__construct($outerIterator, $innerIterator, $joiningFunction);
        $this->outerKeyFunction = Utilities\Functions::allowExcessiveArguments($outerKeyFunction);
        $this->innerKeyFunction = Utilities\Functions::allowExcessiveArguments($innerKeyFunction);
    }

    final protected function initialize()
    {
        $this->innerKeyLookup =
                Utilities\Lookup::fromGroupingFunction(
                        $this->innerKeyFunction,
                        $this->innerIterator);
    }

    final protected function getInnerGroupIterator($outerValue, $outerKey)
    {
        $outerEqualityValueFunction = $this->outerKeyFunction;
        $outerEqualityValue = $outerEqualityValueFunction($outerValue, $outerKey);

        if ($this->innerKeyLookup->contains($outerEqualityValue)) {
            $currentInnerGroup = $this->innerKeyLookup->get($outerEqualityValue);
        } else {
            $currentInnerGroup = [];
        }

        return $this->getInnerGroupValueIterator($currentInnerGroup);
    }

    abstract protected function getInnerGroupValueIterator(array $innerGroup);
}
