<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the array compatible iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayCompatibleIterator extends IteratorIterator
{
    use Common\ArrayCompatibleIterator;

    /**
     * @var int
     */
    private $maxKey = 0;

    /**
     * @var OrderedMap
     */
    private $nonScalarKeyMap;

    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function doRewind()
    {
        $this->maxKey          = 0;
        $this->nonScalarKeyMap = new OrderedMap();
        parent::doRewind();
    }

    protected function doFetch()
    {

        if ($element = $this->iterator->fetch()) {
            $keyCopy = $element[0];
            $this->makeKeyCompatible($keyCopy, $this->maxKey, $this->nonScalarKeyMap);

            return [$keyCopy, &$element[1]];
        }
    }
}
