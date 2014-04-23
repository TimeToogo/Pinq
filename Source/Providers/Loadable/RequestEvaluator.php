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
    
    protected abstract function loadValues(Requests\Values $request);
    
    public final function visitFirst(Requests\First $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitFirst($request) : $this->loadFirst($request);
    }
    
    protected abstract function loadFirst(Requests\First $request);
    
    public final function visitLast(Requests\Last $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitLast($request) : $this->loadLast($request);
    }
    
    protected abstract function loadLast(Requests\Last $request);
    
    public final function visitCount(Requests\Count $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitCount($request) : $this->loadCount($request);
    }
    
    protected abstract function loadCount(Requests\Count $request);
    
    public final function visitExists(Requests\Exists $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitExists($request) : $this->loadExists($request);
    }
    
    protected abstract function loadExists(Requests\Exists $request);
    
    public final function visitContains(Requests\Contains $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitContains($request) : $this->loadContains($request);
    }
    
    protected abstract function loadContains(Requests\Contains $request);
    
    public final function visitAggregate(Requests\Aggregate $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAggregate($request) : $this->loadAggregate($request);
    }
    
    protected abstract function loadAggregate(Requests\Aggregate $request);
    
    public final function visitMaximum(Requests\Maximum $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitMaximum($request) : $this->loadMaximum($request);
    }
    
    protected abstract function loadMaximum(Requests\Maximum $request);
    
    public final function visitMinimum(Requests\Minimum $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitMinimum($request) : $this->loadMinimum($request);
    }
    
    protected abstract function loadMinimum(Requests\Minimum $request);
    
    public final function visitGetIndex(Requests\GetIndex $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitGetIndex($request) : $this->loadGetIndex($request);
    }
    
    protected abstract function loadGetIndex(Requests\GetIndex $request);
    
    public final function visitIssetIndex(Requests\IssetIndex $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitIssetIndex($request) : $this->loadIssetIndex($request);
    }
    
    protected abstract function loadIssetIndex(Requests\IssetIndex $request);
    
    public final function visitSum(Requests\Sum $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitSum($request) : $this->loadSum($request);
    }
    
    protected abstract function loadSum(Requests\Sum $request);
    
    public final function visitAverage(Requests\Average $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAverage($request) : $this->loadAverage($request);
    }
    
    protected abstract function loadAverage(Requests\Average $request);
    
    public final function visitAll(Requests\All $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAll($request) : $this->loadAll($request);
    }
    
    protected abstract function loadAll(Requests\All $request);
    
    public final function visitAny(Requests\Any $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitAny($request) : $this->loadAny($request);
    }
    
    protected abstract function loadAny(Requests\Any $request);
    
    public final function visitImplode(Requests\Implode $request)
    {
        return $this->isLoaded ? $this->loadedRequestEvaluator->visitImplode($request) : $this->loadImplode($request);
    }
    
    protected abstract function loadImplode(Requests\Implode $request);
}