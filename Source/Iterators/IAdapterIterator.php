<?php

namespace Pinq\Iterators;

/**
 * Interface for an adapter iterator.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IAdapterIterator
{
    /**
     * Gets the source iterator.
     *
     * @return \Traversable
     */
    public function getSourceIterator();
}
