<?php

namespace Pinq\Queries\Segments;

use \Pinq\Queries\IScope;

class SegmentWalker
{
    /**
     * @param IScope $Scope
     * @return IScope
     */
    final public function Walk(IScope $Scope)
    {
        $Queries = $Scope->GetSegments();
        foreach ($Queries as $Key => $Query) {
            $Queries[$Key] = $Query->Traverse($this);
        }
        
        return $Scope->Update($Queries);
    }

    public function WalkOperation(Operation $Query)
    {
        return $Query;
    }

    public function WalkRange(Range $Query)
    {
        return $Query;
    }

    public function WalkUnique(Unique $Query)
    {
        return $Query;
    }
    
    public function WalkFilter(Filter $Query)
    {
        return $Query;
    }

    public function WalkGroupBy(GroupBy $Query)
    {
        return $Query;
    }

    public function WalkOrderBy(OrderBy $Query)
    {
        return $Query;
    }

    public function WalkSelect(Select $Query)
    {
        return $Query;
    }

    public function WalkSelectMany(SelectMany $Query)
    {
        return $Query;
    }

    public function WalkIndexBy(IndexBy $Query)
    {
        return $Query;
    }
}
