<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using the fetch method.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class GroupJoinIterator extends JoinIterator
{
    use Common\GroupJoinIterator;

    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $traversableFactory)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructGroupJoinIterator($traversableFactory);
    }
}
