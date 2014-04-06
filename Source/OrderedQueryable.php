<?php

namespace Pinq;

use \Pinq\Queries\Segments;

class OrderedQueryable extends Queryable implements \Pinq\IOrderedTraversable
{
    /**
     * @var Segments\OrderBy 
     */
    private $OrderBy;
    
    public function __construct(Providers\IQueryProvider $Provider, Queries\IScope $Scope)
    {
        $Segments = $Scope->GetSegments();
        $LastSegment = end($Segments);
        
        if(!($LastSegment instanceof Segments\OrderBy)) {
            throw new PinqException('Query scope must end in order by query');
        }
        $this->OrderBy = $LastSegment;
        
        parent::__construct($Provider, $Scope);
    }
    
    public function ThenBy(callable $Function)
    {
        return $this->UpdateLastSegment($this->OrderBy->ThenBy($this->Convert($Function), true));
    }
    
    public function ThenByDescending(callable $Function)
    {
        return $this->UpdateLastSegment($this->OrderBy->ThenBy($this->Convert($Function), false));
    }
}
