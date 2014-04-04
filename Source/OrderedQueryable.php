<?php

namespace Pinq;

class OrderedQueryable extends Queryable implements \Pinq\IOrderedTraversable
{
    /**
     * @var Queries\OrderBy 
     */
    private $OrderBy;
    
    public function __construct(Providers\IQueryProvider $Provider, Providers\IQueryScope $Scope)
    {
        $Queries = $Scope->GetQueryStream()->GetQueries();
        $LastQuery = end($Queries);
        if(!($LastQuery instanceof Queries\OrderBy)) {
            throw new PinqException('Query scope must end in order by query');
        }
        $this->OrderBy = $LastQuery;
        
        parent::__construct($Provider, $Scope);
    }
    
    public function ThenBy(callable $Function)
    {
        return $this->UpdateLastQuery($this->OrderBy->ThenBy($this->Convert($Function), true));
    }
    
    public function ThenByDescending(callable $Function)
    {
        return $this->UpdateLastQuery($this->OrderBy->ThenBy($this->Convert($Function), false));
    }
}
