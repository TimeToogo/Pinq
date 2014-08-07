<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the range iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RangeIterator extends IteratorGenerator
{
    use Common\RangeIterator;

    public function __construct(IGenerator $iterator, $startAmount, $rangeAmount)
    {
        parent::__construct($iterator);
        self::__constructIterator($startAmount, $rangeAmount);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        $start = $this->startPosition;
        $end   = $this->endPosition;

        $position = 0;

        foreach ($iterator as $key => &$value) {
            if ($end !== null && $position >= $end) {
                break;
            } elseif ($position >= $start) {
                yield $key => $value;
            }

            $position++;
        }
    }
}
