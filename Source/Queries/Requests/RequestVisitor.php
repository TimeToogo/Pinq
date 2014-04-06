<?php

namespace Pinq\Queries\Requests;

use \Pinq\Queries\IRequest;

class RequestVisitor
{
    /**
     * @param mixed The returned value
     */
    final public function Visit(IRequest $Request)
    {
        return $Request->Traverse($this);
    }

    public function VisitValues(Values $Request) {}

    public function VisitCount(Count $Request) {}

    public function VisitExists(Exists $Request) {}

    public function VisitFirst(First $Request) {}

    public function VisitLast(Last $Request) {}

    public function VisitContains(Contains $Request) {}

    public function VisitAggregate(Aggregate $Request) {}

    public function VisitMaximum(Maximum $Request) {}

    public function VisitMinimum(Minimum $Request) {}

    public function VisitSum(Sum $Request) {}

    public function VisitAverage(Average $Request) {}

    public function VisitAll(All $Request) {}

    public function VisitAny(Any $Request) {}

    public function VisitImplode(Implode $Request) {}
    
    public function VisitGetIndex(GetIndex $Request) {}
    
    public function VisitIssetIndex(IssetIndex $Request) {}
}
