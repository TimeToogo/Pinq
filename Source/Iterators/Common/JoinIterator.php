<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the join iterator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait JoinIterator
{
    /**
     * @var callable
     */
    protected $projectionFunction;

    /**
     * @var boolean
     */
    protected $hasDefault = false;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var mixed
     */
    protected $defaultKey;

    protected function __constructIterator()
    {
        $this->projectionFunction = function ($outerValue) {
            return $outerValue;
        };
    }

    public function projectTo(callable $function)
    {
        $self                     = clone $this;
        $self->projectionFunction = Functions::allowExcessiveArguments($function);

        return $self;
    }

    public function withDefault($value, $key = null)
    {
        $self               = clone $this;
        $self->hasDefault   = true;
        $self->defaultValue = $value;
        $self->defaultKey   = $key;

        return $self;
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return true;
    }
}
