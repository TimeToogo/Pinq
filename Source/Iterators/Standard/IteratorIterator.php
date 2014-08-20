<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\IWrapperIterator;

/**
 * Base class for wrapper iterators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class IteratorIterator extends Iterator implements IWrapperIterator
{
    /**
     * @var IIterator
     */
    protected $iterator;

    public function __construct(IIterator $iterator)
    {
        parent::__construct();
        $this->iterator = $iterator;
    }

    /**
     * @return IIterator
     */
    final public function getSourceIterator()
    {
        return $this->iterator;
    }

    final public function updateSourceIterator(\Traversable $sourceIterator)
    {
        $sourceIterator = IteratorScheme::adapter($sourceIterator);

        $clone           = clone $this;
        $clone->iterator = $sourceIterator;
        $clone->rewind();

        return $clone;
    }

    protected function doRewind()
    {
        $this->iterator->rewind();
    }
}
