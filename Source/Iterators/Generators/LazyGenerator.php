<?php

namespace Pinq\Iterators\Generators;

/**
 * Base class for a lazy generator, initialized upon rewind.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class LazyGenerator extends IteratorGenerator
{
    public function __construct(IGenerator $iterator)
    {
        parent::__construct($iterator);
    }

    final protected function &iteratorGenerator(IGenerator $iterator)
    {
        $loadedIterator = $this->initializeGenerator($iterator);

        foreach ($loadedIterator as $key => &$value) {
            yield $key => $value;
        }
    }

    /**
     * @param IGenerator $innerIterator
     *
     * @return \Traversable
     */
    abstract protected function initializeGenerator(IGenerator $innerIterator);
}
