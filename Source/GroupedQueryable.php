<?php

namespace Pinq;

use \Pinq\Queries\Segments;

class GroupedQueryable extends Queryable implements \Pinq\IGroupedTraversable
{
    /**
     * @var Segments\GroupBy 
     */
    private $GroupBy;
    
    public function __construct(Providers\IQueryProvider $Provider, Queries\IScope $Scope)
    {
        $Segments = $Scope->GetSegments();
        $LastSegment = end($Segments);
        
        if(!($LastSegment instanceof Segments\GroupBy)) {
            throw new PinqException('Query scope must end in group by query');
        }
        $this->GroupBy = $LastSegment;
        
        parent::__construct($Provider, $Scope);
    }
    
    public function AndBy(callable $Function)
    {
        return $this->UpdateLastSegment($this->GroupBy->AndBy($this->Convert($Function)));
    }
}
