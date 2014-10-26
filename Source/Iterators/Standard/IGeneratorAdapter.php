<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Generators\IGenerator;

/**
 * Implementation of the adapter iterator for generators using the fetch method.
 * This class providers interoperability between the generator scheme and the standard
 * scheme.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IGeneratorAdapter extends IteratorAdapter
{
    /**
     * @var IGenerator
     */
    protected $generator;

    /**
     * @var \Generator
     */
    protected $currentGeneratorWrapper;

    /**
     * @var mixed
     */
    protected $currentKey;

    /**
     * @var mixed
     */
    protected $currentValue;

    public function __construct(IGenerator $generator)
    {
        parent::__construct($generator->getIterator());
        $this->generator = $generator;
    }

    public function doRewind()
    {
        $this->iterator = $this->buildGeneratorWrapper();
        parent::doRewind();
    }

    //Considering this is an adapter for generators, they must be supported
    //hence we should be able to utilise generator in this iterator
    protected function buildGeneratorWrapper()
    {
        foreach ($this->generator as $key => &$value) {
            yield null;
            $this->currentKey   = $key;
            $this->currentValue =& $value;
        }
    }

    protected function doFetch()
    {
        if (parent::doFetch() !== null) {
            return [$this->currentKey, &$this->currentValue];
        }
    }
}
