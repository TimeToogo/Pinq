<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\IRequest;

/**
 * The request visitor is a utility class that will visit any
 * request in a respective method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestVisitor implements IRequestVisitor
{
    /**
     * @param IRequest $request
     *
     * @return mixed
     */
    final public function visit(IRequest $request)
    {
        return $request->traverse($this);
    }

    public function visitValues(Values $request)
    {

    }

    public function visitCount(Count $request)
    {

    }

    public function visitIsEmpty(IsEmpty $request)
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
