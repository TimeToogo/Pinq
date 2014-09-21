<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the join iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class JoinIterator extends IteratorIterator
{
    use Common\JoinIterator;

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

    /**
     * @var mixed
     */
    protected $outerKey;

    /**
     * @var mixed
     */
    protected $outerValue;

    /**
     * @var IIterator
     */
    private $innerValuesIterator;

    public function __construct(IIterator $outerIterator, IIterator $innerIterator)
    {
        parent::__construct($outerIterator);
        self::__constructIterator();
        $this->outerIterator       =& $this->iterator;
        $this->innerIterator       = $innerIterator;
        $this->innerValuesIterator = new EmptyIterator();
    }

    public function walk(callable $function)
    {
        $this->rewind();

        while ($outerElement = $this->outerIterator->fetch()) {
            $innerIterator = $this->getInnerValuesIterator($outerElement[0], $outerElement[1]);
            $innerIterator->rewind();

            while ($innerElement = $innerIterator->fetch()) {
                $function($outerElement[1], $innerElement[1], $outerElement[0], $innerElement[0]);
            }
        }
    }

    protected function doRewind()
    {
        parent::doRewind();
        $this->innerValuesIterator = new EmptyIterator();
        $this->count               = 0;
    }

    protected function doFetch()
    {
        while ((list($innerKey, $innerValue) = $this->innerValuesIterator->fetch()) === null) {
            if ((list($this->outerKey, $this->outerValue) = $this->outerIterator->fetch()) === null) {
                return null;
            }

            $this->innerValuesIterator = $this->getInnerValuesIterator($this->outerKey, $this->outerValue);
            $this->innerValuesIterator->rewind();
        }

        $projectionFunction = $this->projectionFunction;

        return [
                $this->count++,
                $projectionFunction($this->outerValue, $innerValue, $this->outerKey, $innerKey)
        ];
    }

    final protected function defaultIterator(IIterator $iterator)
    {
        return $this->hasDefault ?
                new CoalesceIterator($iterator, $this->defaultValue, $this->defaultKey) : $iterator;
    }

    /**
     * @param mixed $outerKey
     * @param mixed $outerValue
     *
     * @return IIterator
     */
    abstract protected function getInnerValuesIterator($outerKey, $outerValue);
}
