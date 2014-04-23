<?php 

namespace Pinq\Queries\Segments;

/**
 * The segment visitor is a utility class that will visit any
 * segment in a respective method.
 * 
 * This is used by the query providers to as to evaluate the
 * scope of the query with the specified query segments
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SegmentVisitor extends SegmentWalker
{
    public final function walkOperation(Operation $query)
    {
        $this->visitOperation($query);
        
        return $query;
    }
    
    public function visitOperation(Operation $query)
    {
        
    }
    
    public final function walkRange(Range $query)
    {
        $this->visitRange($query);
        
        return $query;
    }
    
    public function visitRange(Range $query)
    {
        
    }
    
    public final function walkUnique(Unique $query)
    {
        $this->visitUnique($query);
        
        return $query;
    }
    
    public function visitUnique(Unique $query)
    {
        
    }
    
    public final function walkFilter(Filter $query)
    {
        $this->visitFilter($query);
        
        return $query;
    }
    
    public function visitFilter(Filter $query)
    {
        
    }
    
    public final function walkGroupBy(GroupBy $query)
    {
        $this->visitGroupBy($query);
        
        return $query;
    }
    
    public function visitGroupBy(GroupBy $query)
    {
        
    }
    
    public final function walkJoin(Join $query)
    {
        $this->visitJoin($query);
        
        return $query;
    }
    
    public function visitJoin(Join $query)
    {
        
    }
    
    public final function walkEqualityJoin(EqualityJoin $query)
    {
        $this->visitEqualityJoin($query);
        
        return $query;
    }
    
    public function visitEqualityJoin(EqualityJoin $query)
    {
        
    }
    
    public final function walkOrderBy(OrderBy $query)
    {
        $this->visitOrderBy($query);
        
        return $query;
    }
    
    public function visitOrderBy(OrderBy $query)
    {
        
    }
    
    public final function walkSelect(Select $query)
    {
        $this->visitSelect($query);
        
        return $query;
    }
    
    public function visitSelect(Select $query)
    {
        
    }
    
    public final function walkSelectMany(SelectMany $query)
    {
        $this->visitSelectMany($query);
        
        return $query;
    }
    
    public function visitSelectMany(SelectMany $query)
    {
        
    }
    
    public final function walkIndexBy(IndexBy $query)
    {
        $this->visitIndexBy($query);
        
        return $query;
    }
    
    protected function visitIndexBy(IndexBy $query)
    {
        
    }
}