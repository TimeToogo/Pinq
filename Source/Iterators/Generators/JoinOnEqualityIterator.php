<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoinOnEqualityIterator extends JoinIterator
{
    use Common\JoinOnEqualityIterator;

    public function __construct(
            IGenerator $outerIterator,
            IGenerator $innerIterator,
            callable $outerKeyFunction,
            callable $innerKeyFunction
    ) {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructJoinOnEqualityIterator($outerKeyFunction, $innerKeyFunction);
    }


    protected function beforeOuterLoopData()
    {
        return [
                'innerGroups' => (new OrderedMap($this->innerIterator))->groupBy($this->innerKeyFunction),
        ];
    }


    protected function innerGenerator($outerKey, $outerValue, array $outerData)
    {
        $outerKeyFunction = $this->outerKeyFunction;
        $groupKey         = $outerKeyFunction($outerValue, $outerKey);
        $innerGroups      = $outerData['innerGroups'];

        return $this->defaultIterator(
                $innerGroups->contains($groupKey) && $groupKey !== null ?
                        $innerGroups->get($groupKey) : new EmptyIterator()
        );
    }
}
