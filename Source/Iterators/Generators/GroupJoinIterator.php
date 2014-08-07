<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class GroupJoinIterator extends JoinIterator
{
    use Common\GroupJoinIterator;

    public function __construct(IGenerator $outerIterator, IGenerator $innerIterator, callable $traversableFactory)
    {
        parent::__construct($outerIterator, $innerIterator);
        self::__constructGroupJoinIterator($traversableFactory);
    }
}
