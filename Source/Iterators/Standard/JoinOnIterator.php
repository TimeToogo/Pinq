<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoinOnIterator extends JoinIterator
{
    use Common\JoinOnIterator;

    /**
     * @var OrderedMap
     */
    protected $innerValues;

    public function __construct(IIterator $outerIterator, IIterator $innerIterator, callable $filter)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructJoinOnIterator($filter);
    }

    protected function doRewind()
    {
        $this->innerValues = new OrderedMap($this->innerIterator);
        parent::doRewind();
    }

    protected function getInnerValuesIterator($outerKey, $outerValue)
    {
        return $this->defaultIterator(
                new FilterIterator(
                        $this->innerValues,
                        $this->innerElementFilter($outerKey, $outerValue))
        );
    }
}
