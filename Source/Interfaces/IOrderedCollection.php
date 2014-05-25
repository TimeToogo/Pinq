<?php

namespace Pinq\Interfaces;

use Pinq\ICollection;

/**
 * The API for subsequent orderings of a ICollection
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IOrderedCollection extends IOrderedTraversable, ICollection
{
    /**
     * {@inheritDoc}
     * @return IOrderedCollection
     */
    public function thenBy(callable $function, $direction);

    /**
     * {@inheritDoc}
     * @return IOrderedCollection
     */
    public function thenByAscending(callable $function);

    /**
     * {@inheritDoc}
     * @return IOrderedCollection
     */
    public function thenByDescending(callable $function);
}
