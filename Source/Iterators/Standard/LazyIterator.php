<?php

namespace Pinq\Iterators\Standard;

/**
 * Base class for a lazy iterator, initialized upon rewind.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class LazyIterator extends IteratorIterator
{
    /**
     * @var IIterator
     */
    private $originalIterator;

    public function __construct(IIterator $iterator)
    {
        parent::__construct($iterator);
        $this->originalIterator = $iterator;
    }

    /**
     * @param IIterator $innerIterator
     *
     * @return IIterator
     */
    abstract protected function initializeIterator(IIterator $innerIterator);

    public function doRewind()
    {
        $this->iterator = $this->initializeIterator($this->originalIterator);

        parent::doRewind();
    }

    protected function doFetch()
    {
        return $this->iterator->fetch();
    }
}
