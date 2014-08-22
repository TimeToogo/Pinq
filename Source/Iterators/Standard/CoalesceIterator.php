<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the coalesce using the fetch method
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CoalesceIterator extends IteratorIterator
{
    use Common\CoalesceIterator;

    /**
     * @var boolean
     */
    private $isEmpty = true;

    public function __construct(IIterator $iterator, $defaultValue, $defaultKey)
    {
        parent::__construct($iterator);
        self::__constructIterator($defaultValue, $defaultKey);
    }

    protected function doRewind()
    {
        parent::doRewind();
        $this->isEmpty = true;
    }

    protected function doFetch()
    {
        if ($element = $this->iterator->fetch()) {
            $this->isEmpty = false;

            return $element;
        } elseif ($this->isEmpty) {
            $this->isEmpty = false;

            return [$this->defaultKey, &$this->defaultValue];
        }
    }
}
