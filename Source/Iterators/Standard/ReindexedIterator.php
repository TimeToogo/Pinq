<?php

namespace Pinq\Iterators\Standard;

/**
 * Implementation of the reindexer iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ReindexedIterator extends IteratorIterator
{
    /**
     * @var int
     */
    private $index = 0;

    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function doRewind()
    {
        $this->index = 0;
        parent::doRewind();
    }

    protected function doFetch()
    {
        if ($element = $this->iterator->fetch()) {
            $element[0] = $this->index++;

            return $element;
        }
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return true;
    }
}
