<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GroupJoinOnEqualityIterator extends GroupJoinIterator
{
    use Common\JoinOnEqualityIterator;

    /**
     * @var OrderedMap
     */
    protected $innerGroups;

    public function __construct(
            IIterator $outerIterator,
            IIterator $innerIterator,
            callable $traversableFactory,
            callable $outerKeyFunction,
            callable $innerKeyFunction
    ) {
        parent::__construct($outerIterator, $innerIterator, $traversableFactory);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }

    protected function doRewind()
    {
        $this->innerGroups = (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction);
        parent::doRewind();
    }

    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $groupKey         = $outerKeyFunction($outerValue, $outerKey);

        $traversableGroup = $this->constructInnerGroup(
                $this->defaultIterator(
                        $this->innerGroups->contains($groupKey) && $groupKey !== null ?
                                $this->innerGroups->get($groupKey) : new EmptyIterator()
                )
        );

        return new ArrayIterator([$groupKey => $traversableGroup]);
    }

}
