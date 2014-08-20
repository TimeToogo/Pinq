<?php

namespace Pinq\Queries\Requests;

/**
 * Interface of the request visitor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestVisitor
{
    public function visitAverage(Average $request);

    public function visitLast(Last $request);

    public function visitGetIndex(GetIndex $request);

    public function visitIsEmpty(IsEmpty $request);

    public function visitValues(Values $request);

    public function visitFirst(First $request);

    public function visitAggregate(Aggregate $request);

    public function visitContains(Contains $request);

    public function visitMinimum(Minimum $request);

    public function visitMaximum(Maximum $request);

    public function visitAll(All $request);

    public function visitIssetIndex(IssetIndex $request);

    public function visitSum(Sum $request);

    public function visitAny(Any $request);

    public function visitCount(Count $request);

    public function visitImplode(Implode $request);
}
