<?php

namespace Pinq;

interface IOrderedTraversable extends ITraversable
{
    /**
     * Subsequently orders the results using the supplied function according to
     * the supplied direction
     *
     * @param  callable          $Function
     * @param  int               $Direction
     * @return IOrderedTraversable
     */
    public function ThenBy(callable $Function, $Direction);
    
    /**
     * Subsequently orders the results using the supplied function ascendingly
     *
     * @param  callable          $Function
     * @return IOrderedTraversable
     */
    public function ThenByAscending(callable $Function);

    /**
     * Subsequently orders the results using the supplied function descendingly
     *
     * @param  callable          $Function
     * @return IOrderedTraversable
     */
    public function ThenByDescending(callable $Function);
}
