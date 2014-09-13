<?php

namespace Pinq\Interfaces;

use Pinq\ITraversable;

/**
 * The API for subsequent orderings of a ITraversable
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOrderedTraversable extends ITraversable
{
    const IORDERED_TRAVERSABLE_TYPE = __CLASS__;

    /**
     * Subsequently orders the results using the supplied function according to
     * the supplied direction
     *
     * @param callable $function
     * @param int      $direction
     *
     * @return IOrderedTraversable
     */
    public function thenBy(callable $function, $direction);

    /**
     * Subsequently orders the results using the supplied function ascendingly
     *
     * @param callable $function
     *
     * @return IOrderedTraversable
     */
    public function thenByAscending(callable $function);

    /**
     * Subsequently orders the results using the supplied function descendingly
     *
     * @param callable $function
     *
     * @return IOrderedTraversable
     */
    public function thenByDescending(callable $function);
}
