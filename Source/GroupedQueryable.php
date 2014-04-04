<?php

namespace Pinq;

class GroupedQueryable extends Queryable implements \Pinq\IGroupedTraversable
{
    /**
     * @var Queries\GroupBy 
     */
    private $GroupBy;
    
    public function __construct(Providers\IQueryProvider $Provider, Providers\IQueryScope $Scope)
    {
        $Queries = $Scope->GetQueryStream()->GetQueries();
        $LastQuery = end($Queries);
        if(!($LastQuery instanceof Queries\GroupBy)) {
            throw new PinqException('Query scope must end in group by query');
        }
        $this->GroupBy = $LastQuery;
        
        parent::__construct($Provider, $Scope);
    }
    
    public function AndBy(callable $Function)
    {
        return $this->UpdateLastQuery($this->GroupBy->AndBy($this->Convert($Function)));
    }
}
