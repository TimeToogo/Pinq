<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\IWrapperIterator;

/**
 * Base class for wrapper generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class IteratorGenerator extends Generator implements IWrapperIterator
{
    /**
     * @var IGenerator
     */
    protected $iterator;

    public function __construct(IGenerator $iterator)
    {
        parent::__construct();
        $this->iterator = $iterator;
    }

    final public function getSourceIterator()
    {
        return $this->iterator;
    }

    final public function updateSourceIterator(\Traversable $sourceIterator)
    {
        $clone           = clone $this;
        $clone->iterator = GeneratorScheme::adapter($sourceIterator);

        return $clone;
    }

    final public function &getIterator(): \Traversable
    {
        foreach($this->iteratorGenerator($this->iterator) as $key => &$value) {
            yield $key => $value;
        }
    }

    abstract protected function &iteratorGenerator(IGenerator $iterator);
}
