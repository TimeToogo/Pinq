<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Generators\IGenerator;

/**
 * Implementation of the adapter iterator for generators using the fetch method.
 * This class providers interopability between the generator scheme and the standard
 * scheme.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IGeneratorAdapter extends IteratorAdapter
{
    /**
     * @var IGenerator
     */
    private $generator;

    public function __construct(IGenerator $generator)
    {
        parent::__construct($generator->getIterator());
        $this->generator = $generator;
    }

    public function doRewind()
    {
        //IGenerator is an \IteratorAggregate and a new generator must be built for every rewind
        $this->iterator = $this->generator->getIterator();
        parent::doRewind();
    }
}
