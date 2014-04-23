<?php 

namespace Pinq\Queries\Requests;

use Pinq\Queries\IRequest;

/**
 * The operation visitor is a utility class that will visit any
 * request in a respective method.
 * 
 * This is used by the query providers to as to load the
 * supplied request query
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestVisitor
{
    /**
     * @param mixed The returned value
     */
    public final function visit(IRequest $request)
    {
        return $request->traverse($this);
    }
    
    public function visitValues(Values $request)
    {
        
    }
    
    public function visitCount(Count $request)
    {
        
    }
    
    public function visitExists(Exists $request)
    {
        
    }
    
    public function visitFirst(First $request)
    {
        
    }
    
    public function visitLast(Last $request)
    {
        
    }
    
    public function visitContains(Contains $request)
    {
        
    }
    
    public function visitAggregate(Aggregate $request)
    {
        
    }
    
    public function visitMaximum(Maximum $request)
    {
        
    }
    
    public function visitMinimum(Minimum $request)
    {
        
    }
    
    public function visitSum(Sum $request)
    {
        
    }
    
    public function visitAverage(Average $request)
    {
        
    }
    
    public function visitAll(All $request)
    {
        
    }
    
    public function visitAny(Any $request)
    {
        
    }
    
    public function visitImplode(Implode $request)
    {
        
    }
    
    public function visitGetIndex(GetIndex $request)
    {
        
    }
    
    public function visitIssetIndex(IssetIndex $request)
    {
        
    }
}