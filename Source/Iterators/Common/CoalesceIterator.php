<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIterator;

/**
 * Common functionality for the coalesce iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait CoalesceIterator
{
    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var mixed
     */
    protected $defaultKey;

    public function __constructIterator($defaultValue, $defaultKey)
    {
        $this->defaultValue = $defaultValue;
        $this->defaultKey   = $defaultKey;
    }

    /**
     * @return IIterator
     */
    abstract protected function getSourceIterator();

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return $this->getSourceIterator()->isArrayCompatible();
    }
}
