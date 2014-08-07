<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the coalesce using generators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CoalesceIterator extends IteratorGenerator
{
    use Common\CoalesceIterator;

    public function __construct(IGenerator $iterator, $defaultValue, $defaultKey)
    {
        parent::__construct($iterator);
        self::__constructIterator($defaultValue, $defaultKey);
    }

    protected function &iteratorGenerator(IGenerator $iterator)
    {
        $isEmpty = true;
        foreach ($iterator as $key => &$value) {
            yield $key => $value;
            $isEmpty = false;
        }

        if ($isEmpty) {
            yield $this->defaultKey => $this->defaultValue;
        }
    }
}
