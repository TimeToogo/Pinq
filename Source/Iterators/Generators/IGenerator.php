<?php

namespace Pinq\Iterators\Generators;

/**
 * This a wrapper for Generator object, maintaining typical foreach
 * behaviour while also allowing generators to be 'rewound' without the
 * overhead of a wrapper iterato. This is accomplished by means of 
 * calling the generator function on every call to the 
 * IteratorAggregate::getIterator which is called on every foreach loop. 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IGenerator extends \IteratorAggregate
{
    /**
     * Returns a new generator instance.
     * 
     * @return \Generator
     */
    public function &getIterator();
}
