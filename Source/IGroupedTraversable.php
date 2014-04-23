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
     * @param  callable $function
     * @return IGroupedTraversable
     */
    public function andBy(callable $function);
}