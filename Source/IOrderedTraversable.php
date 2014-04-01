<?php

namespace Pinq;

/**
 * Concrete classes should be immutable and return a new instance with every chained query call.
 */
interface IOrderedTraversable extends ITraversable
{
    /**
     * Specifies the function to use for subsequent ascending ordering
     *
     * @param  callable           $Function
     * @return IOrderedTraversable
     */
    public function ThenBy(callable $Function);

    /**
     * Specifies the function to use for subsequent descending ordering.
     *
     * @param  callable           $Function
     * @return IOrderedTraversable
     */
    public function ThenByDescending(callable $Function);
}
