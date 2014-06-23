<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the projection iterator using generators.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProjectionIterator extends IteratorGenerator
{
    use Common\ProjectionIterator;

    public function __construct(\Traversable $iterator, callable $keyProjectionFunction = null, callable $valueProjectionFunction = null)
    {
        parent::__construct($iterator);
        self::__constructIterator($keyProjectionFunction, $valueProjectionFunction);
    }
    
    protected function &iteratorGenerator(\Traversable $iterator)
    {
        foreach($iterator as $key => &$value) {
            $element = $this->projectElement($key, $value);
            
            yield $element[0] => $element[1];
            unset($value);
        }
    }
}
