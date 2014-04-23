<?php 

namespace Pinq\Queries;

/**
 * The query scope. This contains many query segments which
 * in order represent the scope of the query to be loaded/executed.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IScope extends \IteratorAggregate
{
    /**
     * @return ISegment[]
     */
    public function getSegments();
    
    /**
     * @return boolean
     */
    public function isEmpty();
    
    /**
     * @return IScope
     */
    public function append(ISegment $segment);
    
    /**
     * @return IScope
     */
    public function update(array $segments);
    
    /**
     * @return IScope
     */
    public function updateLast(ISegment $segment);
}