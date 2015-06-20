<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\IJoinIterator;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnfilteredGroupJoinIterator extends GroupJoinIterator implements IJoinIterator
{
    public function filterOn(callable $function)
    {
        return new GroupJoinOnIterator(
                $this->outerIterator,
                $this->innerIterator,
                $this->traversableFactory,
                $function);
    }

    public function filterOnEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new GroupJoinOnEqualityIterator(
                $this->outerIterator,
                $this->innerIterator,
                $this->traversableFactory,
                $outerKeyFunction,
                $innerKeyFunction);
    }


    protected function beforeOuterLoopData()
    {
        return [
                'innerGroup' => new OrderedMap($this->defaultIterator($this->innerIterator)),
        ];
    }


    protected function innerGenerator($outerKey, $outerValue, array $outerData)
    {
        $traversableFactory = $this->traversableFactory;
        $innerGroup         = $traversableFactory($outerData['innerGroup']);

        return new ArrayIterator([0 => $innerGroup]);
    }
}
