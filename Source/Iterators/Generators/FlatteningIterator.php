<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;

/**
 * Implementation of the flattened iterator using generators.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FlatteningIterator extends IteratorGenerator
{
    protected function &iteratorGenerator(IGenerator $iterator)
    {
        foreach ($iterator as $innerIterator) {
            $innerIterator = GeneratorScheme::adapter($innerIterator);

            foreach ($innerIterator as &$value) {
                yield $value;
            }
        }
    }
}
