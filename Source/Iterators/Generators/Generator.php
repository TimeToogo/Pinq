<?php

namespace Pinq\Iterators\Generators;

/**
 * Base class for an iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Generator implements IGenerator
{
    public function __construct()
    {

    }

    /**
     * {@inheritDoc}
     */
    abstract public function &getIterator(): \Traversable;
}
