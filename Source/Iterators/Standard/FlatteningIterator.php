<?php

namespace Pinq\Iterators\Standard;

/**
 * Implementation of the flattening iterator using the fetch method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FlatteningIterator extends Iterator
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var IIterator
     */
    protected $outerIterator;

    /**
     * @var IIterator
     */
    protected $innerIterator;

    public function __construct(IIterator $iterator)
    {
        parent::__construct();
        $this->outerIterator = $iterator;
    }

    public function doRewind()
    {
        $this->count = 0;
        $this->outerIterator->rewind();
        $this->innerIterator = new EmptyIterator();
    }

    protected function doFetch()
    {
        while (($innerElement = $this->innerIterator->fetch()) === null) {
            if (($outerElement = $this->outerIterator->fetch()) === null) {
                return null;
            }

            $outerValue = IteratorScheme::adapter($outerElement[1]);

            $this->innerIterator = $outerValue;
            $this->innerIterator->rewind();
        }

        return [$this->count++, &$innerElement[1]];
    }

    /**
     * @return bool
     */
    public function isArrayCompatible()
    {
        return true;
    }
}
