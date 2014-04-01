<?php

namespace Pinq\Queries;

abstract class QueryStreamVisitor
{
    /**
     * @param IQueryStream $QueryStream
     */
    final public function Visit(IQueryStream $QueryStream)
    {
        $Stream = $QueryStream->GetStream();
        foreach ($Stream as $Key => $Query) {
            $Query->Traverse($this);
        }
    }

    public function VisitOperation(Operation $Query)
    {
    }

    public function VisitRange(Range $Query)
    {
    }

    public function VisitUnique(Unique $Query)
    {
    }
}
