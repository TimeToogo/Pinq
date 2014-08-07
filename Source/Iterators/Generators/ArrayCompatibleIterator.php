<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the array compatible iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayCompatibleIterator extends IteratorGenerator
{
    use Common\ArrayCompatibleIterator;

    public function __construct(IGenerator $iterator)
    {
        parent::__construct($iterator);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        $maxKey          = 0;
        $nonScalarKeyMap = new OrderedMap();

        foreach ($iterator as $key => &$value) {
            $this->makeKeyCompatible($key, $maxKey, $nonScalarKeyMap);

            yield $key => $value;
        }
    }
}
