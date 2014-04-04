<?php

namespace Pinq;

interface IGroupedTraversable extends ITraversable
{
    /**
     * Specifies the function to use for grouping
     *
     * @param  callable $Function
     * @return IGroupedTraversable
     */
    public function AndBy(callable $Function);
}
