<?php

namespace Pinq\Providers\Caching;

use Pinq\Queries;
use Pinq\Queries\Requests;

/**
 * A caching request evaluator instance, the inner evaluators
 * results will be cached and reused if an equivalent request is
 * called again.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestEvaluator extends Requests\RequestVisitor
{
    /**
     * @var Requests\RequestVisitor
     */
    private $innerRequestEvaluator;

    /**
     * @var array<string, mixed>
     */
    private $methodResultCache = [];

    public function __construct(Requests\RequestVisitor $innerRequestEvaluator)
    {
        $this->innerRequestEvaluator = $innerRequestEvaluator;
    }

    private function cacheMethodResult($methodName, Queries\IRequest $request)
    {
        $key = $methodName . '-' . md5(serialize($request));

        if (!isset($this->methodResultCache[$key])) {
            $this->methodResultCache[$key] = $this->innerRequestEvaluator->{$methodName}($request);
        }

        return $this->methodResultCache[$key];
    }

    public function visitValues(Requests\Values $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitCount(Requests\Count $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitIsEmpty(Requests\IsEmpty $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitFirst(Requests\First $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitLast(Requests\Last $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitContains(Requests\Contains $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitSum(Requests\Sum $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitAverage(Requests\Average $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitAll(Requests\All $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitAny(Requests\Any $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }

    public function visitImplode(Requests\Implode $request)
    {
        return $this->cacheMethodResult(__FUNCTION__, $request);
    }
}
