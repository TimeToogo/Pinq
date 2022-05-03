<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\IIterator;

/**
 * This a wrapper for Generator object, maintaining typical foreach
 * behaviour while also allowing generators to be 'rewound' without the
 * overhead of a wrapper iterator. This is accomplished by means of
 * calling the generator function on every call to the
 * IteratorAggregate::getIterator which is called on every foreach loop.
 * The IGenerator class is able to be used with foreach-by-ref.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IGenerator extends \IteratorAggregate, IIterator
{
    /**
     * Returns a new generator instance.
     *
     * @return \Generator
     */
    public function &getIterator(): \Traversable;
}
