<?php

namespace Pinq\Interfaces;

use Pinq\ICollection;

/**
 * The API for subsequent orderings of a ICollection
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOrderedCollection extends ICollection, IOrderedTraversable
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
