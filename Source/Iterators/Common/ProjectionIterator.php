<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIterator;

/**
 * Common functionality for the projection iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait ProjectionIterator
{
    /**
     * @var callable|null
     */
    private $keyProjectionFunction;

    /**
     * @var callable|null
     */
    private $valueProjectionFunction;

    protected function __constructIterator(
            callable $keyProjectionFunction = null,
            callable $valueProjectionFunction = null
    ) {
        $this->keyProjectionFunction   = $keyProjectionFunction === null ?
                null : Functions::allowExcessiveArguments($keyProjectionFunction);
        $this->valueProjectionFunction = $valueProjectionFunction === null ?
                null : Functions::allowExcessiveArguments($valueProjectionFunction);
    }

    final protected function projectElement(&$key, &$value)
    {
        $keyProjectionFunction   = $this->keyProjectionFunction;
        $valueProjectionFunction = $this->valueProjectionFunction;

        $keyCopy   = $key;
        $valueCopy = $value;

        if ($keyProjectionFunction !== null) {
            $keyCopyForKey   = $keyCopy;
            $valueCopyForKey = $valueCopy;

            $keyProjection = $keyProjectionFunction($valueCopyForKey, $keyCopyForKey);
        } else {
            $keyProjection =& $key;
        }

        if ($valueProjectionFunction !== null) {
            $keyCopyForValue   = $keyCopy;
            $valueCopyForValue = $valueCopy;

            $valueProjection = $valueProjectionFunction($valueCopyForValue, $keyCopyForValue);
        } else {
            $valueProjection =& $value;
        }

        return [&$keyProjection, &$valueProjection];
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
        return $this->keyProjectionFunction === null && $this->getSourceIterator()->isArrayCompatible();
    }
}
