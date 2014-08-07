<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class GroupJoinIterator extends JoinIterator
{
    use Common\GroupJoinIterator;

    public function __construct(IIterator $outerIterator, IIterator $innerIterator, callable $traversableFactory)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructGroupJoinIterator($traversableFactory);
    }
}
