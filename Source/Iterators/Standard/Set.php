<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\ISet;

/**
 * Implementation of the set using the fetch method for iteration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Set implements \IteratorAggregate, ISet
{
    use Common\Set;

    public function __construct(IIterator $values = null)
    {
        if ($values !== null) {
            $values->rewind();
            while ($element = $values->fetch()) {
                $this->addRef($element[1]);
            }
        }
    }

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->values);
    }
}
