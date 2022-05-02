<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IAdapterIterator;

/**
 * Implementation of the adapter iterator for standard iterators using the generator
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IteratorAdapter extends Generator implements IAdapterIterator
{
    use Common\AdapterIterator;

    public function __construct(\Traversable $iterator)
    {
        parent::__construct();
        self::__constructIterator($iterator);
    }

    public function &getIterator(): \Traversable
    {
        foreach ($this->iterator as $key => $value) {
            yield $key => $value;
            unset($value);
        }
    }

}
