<?php

namespace Pinq;

/**
 * Api for subsequent grouping of the supplied traversable query.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IGroupedTraversable extends ITraversable
{
    /**
     * Subsequently groups values according the supplied function. (Uses strict equality '===')
     *
     * @param  callable $Function
     * @return IGroupedTraversable
     */
    public function AndBy(callable $Function);
}
