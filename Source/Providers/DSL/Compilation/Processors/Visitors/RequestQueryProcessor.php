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
        $request->traverse($this);

        return $this->request;
    }

    public function visitAverage(Requests\Average $request)
    {
        $this->request = $request;
    }

    public function visitLast(Requests\Last $request)
    {
        $this->request = $request;
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        $this->request = $request;
    }

    public function visitIsEmpty(Requests\IsEmpty $request)
    {
        $this->request = $request;
    }

    public function visitValues(Requests\Values $request)
    {
        $this->request = $request;
    }

    public function visitFirst(Requests\First $request)
    {
        $this->request = $request;
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        $this->request = $request;
    }

    public function visitContains(Requests\Contains $request)
    {
        $this->request = $request;
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        $this->request = $request;
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        $this->request = $request;
    }

    public function visitAll(Requests\All $request)
    {
        $this->request = $request;
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        $this->request = $request;
    }

    public function visitSum(Requests\Sum $request)
    {
        $this->request = $request;
    }

    public function visitAny(Requests\Any $request)
    {
        $this->request = $request;
    }

    public function visitCount(Requests\Count $request)
    {
        $this->request = $request;
    }

    public function visitImplode(Requests\Implode $request)
    {
        $this->request = $request;
    }
}
