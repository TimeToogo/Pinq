<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Visitors;

use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries\Requests;
use Pinq\Queries;

/**
 * Implementation of the request query processor using the visitor pattern.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestQueryProcessor extends Processors\RequestQueryProcessor implements Requests\IRequestVisitor
{
    /**
     * @var Queries\IRequest
     */
    protected $request;

    protected function processRequest(Queries\IScope $scope, Queries\IRequest $request)
    {
        $this->request = $request->traverse($this);

        return $this->request;
    }

    public function visitAverage(Requests\Average $request)
    {
        return $request;
    }

    public function visitLast(Requests\Last $request)
    {
        return $request;
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        return $request;
    }

    public function visitIsEmpty(Requests\IsEmpty $request)
    {
        return $request;
    }

    public function visitValues(Requests\Values $request)
    {
        return $request;
    }

    public function visitFirst(Requests\First $request)
    {
        return $request;
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        return $request;
    }

    public function visitContains(Requests\Contains $request)
    {
        return $request;
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        return $request;
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        return $request;
    }

    public function visitAll(Requests\All $request)
    {
        return $request;
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        return $request;
    }

    public function visitSum(Requests\Sum $request)
    {
        return $request;
    }

    public function visitAny(Requests\Any $request)
    {
        return $request;
    }

    public function visitCount(Requests\Count $request)
    {
        return $request;
    }

    public function visitImplode(Requests\Implode $request)
    {
        return $request;
    }
}
