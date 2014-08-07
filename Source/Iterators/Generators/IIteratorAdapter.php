<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Standard\IIterator;

/**
 * Implementation of the adapter iterator for Pinq\Iterators\IIterator using the generator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IIteratorAdapter extends Generator
{
    /**
     * @var IIterator
     */
    private $iterator;

    public function __construct(IIterator $iterator)
    {
        parent::__construct();

        $this->iterator = $iterator;
    }

    public function &getIterator()
    {
        $this->iterator->rewind();
        while ($element = $this->iterator->fetch()) {
            yield $element[0] => $element[1];
        }
    }
}
