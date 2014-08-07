<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the projection iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ProjectionIterator extends IteratorGenerator
{
    use Common\ProjectionIterator;

    public function __construct(
            IGenerator $iterator,
            callable $keyProjectionFunction = null,
            callable $valueProjectionFunction = null
    ) {
        parent::__construct($iterator);
        self::__constructIterator($keyProjectionFunction, $valueProjectionFunction);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        foreach ($iterator as $key => &$value) {
            $element = $this->projectElement($key, $value);

            yield $element[0] => $element[1];
            unset($value);
        }
    }
}
