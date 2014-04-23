<?php

namespace Pinq\Providers\Loadable;

use Pinq\Queries;
use Pinq\Queries\Requests;
use Pinq\Providers\Traversable;

/**
 * Base request evaluator for request evaluator in which the values
 * can be loaded and once loaded, the requests can be performed in memory.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class RequestEvaluator extends Requests\RequestVisitor
{
    /**
     * @var Traversable\RequestEvaluator
     */
    private $loadedRequestEvaluator;

    /**
     * @var boolean
     */
    private $isLoaded = false;

    public function __construct()
    {

    }

    public function visitValues(Requests\Values $request)
    {
        if (!$this->isLoaded) {
            $traversable = new \Pinq\Traversable($this->loadValues($request));
            $this->loadedRequestEvaluator = new Traversable\RequestEvaluator($traversable);
            $this->isLoaded = true;
        }

        return $this->loadedRequestEvaluator->visitValues($request);
    }

    abstract protected function loadValues(Requests\Values $request);

    final public function visitFirst(Requests\First $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitFirst($request) : $this->loadFirst($request);
    }

    abstract protected function loadFirst(Requests\First $request);

    final public function visitLast(Requests\Last $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitLast($request) : $this->loadLast($request);
    }

    abstract protected function loadLast(Requests\Last $request);

    final public function visitCount(Requests\Count $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitCount($request) : $this->loadCount($request);
    }

    abstract protected function loadCount(Requests\Count $request);

    final public function visitExists(Requests\Exists $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitExists($request) : $this->loadExists($request);
    }

    abstract protected function loadExists(Requests\Exists $request);

    final public function visitContains(Requests\Contains $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitContains($request) : $this->loadContains($request);
    }

    abstract protected function loadContains(Requests\Contains $request);

    final public function visitAggregate(Requests\Aggregate $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAggregate($request) : $this->loadAggregate($request);
    }

    abstract protected function loadAggregate(Requests\Aggregate $request);

    final public function visitMaximum(Requests\Maximum $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitMaximum($request) : $this->loadMaximum($request);
    }

    abstract protected function loadMaximum(Requests\Maximum $request);

    final public function visitMinimum(Requests\Minimum $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitMinimum($request) : $this->loadMinimum($request);
    }

    abstract protected function loadMinimum(Requests\Minimum $request);

    final public function visitGetIndex(Requests\GetIndex $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitGetIndex($request) : $this->loadGetIndex($request);
    }

    abstract protected function loadGetIndex(Requests\GetIndex $request);

    final public function visitIssetIndex(Requests\IssetIndex $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitIssetIndex($request) : $this->loadIssetIndex($request);
    }

    abstract protected function loadIssetIndex(Requests\IssetIndex $request);

    final public function visitSum(Requests\Sum $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitSum($request) : $this->loadSum($request);
    }

    abstract protected function loadSum(Requests\Sum $request);

    final public function visitAverage(Requests\Average $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAverage($request) : $this->loadAverage($request);
    }

    abstract protected function loadAverage(Requests\Average $request);

    final public function visitAll(Requests\All $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAll($request) : $this->loadAll($request);
    }

    abstract protected function loadAll(Requests\All $request);

    final public function visitAny(Requests\Any $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAny($request) : $this->loadAny($request);
    }

    abstract protected function loadAny(Requests\Any $request);

    final public function visitImplode(Requests\Implode $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitImplode($request) : $this->loadImplode($request);
    }

    abstract protected function loadImplode(Requests\Implode $request);
}
