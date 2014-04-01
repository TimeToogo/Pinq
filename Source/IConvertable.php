<?php

namespace Pinq;

/**
 * Concrete classes should be immutable and return a new instance with every chained query call.
 */
interface IConvertable
{
    /**
     * Returns the values as an array
     *
     * @return array
     */
    public function AsArray();

    /**
     * Returns the values a traversable
     *
     * @return ITraversable
     */
    public function AsTraversable();

    /**
     * Returns the values a collection
     *
     * @return ICollection
     */
    public function AsCollection();

}
