<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\IJoinIterator;
use Pinq\Iterators\IJoinToIterator;

/**
 * Implements the filtering API for a join / group join traversable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoiningTraversable implements Interfaces\IJoiningOnTraversable
{
    /**
     * @var IIteratorScheme
     */
    protected $scheme;

    /**
     * @var IJoinIterator|IJoinToIterator
     */
    protected $joinIterator;

    /**
     * @var callable
     */
    protected $traversableFactory;

    public function __construct(IIteratorScheme $scheme, IJoinIterator $joinIterator, callable $traversableFactory)
    {
        $this->scheme             = $scheme;
        $this->joinIterator       = $joinIterator;
        $this->traversableFactory = $traversableFactory;
    }

    public function on(callable $joiningOnFunction)
    {
        $this->joinIterator = $this->joinIterator->filterOn($joiningOnFunction);

        return $this;
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        $this->joinIterator = $this->joinIterator->filterOnEquality($outerKeyFunction, $innerKeyFunction);

        return $this;
    }

    public function withDefault($value, $key = null)
    {
        $this->joinIterator = $this->joinIterator->withDefault($value, $key);

        return $this;
    }

    public function to(callable $joinFunction)
    {
        $traversableFactory = $this->traversableFactory;

        return $traversableFactory($this->joinIterator->projectTo($joinFunction));
    }
}
