<?php

namespace Pinq\Providers\Loadable;

use \Pinq\Queries;
use \Pinq\Queries\Requests;
use \Pinq\Providers\Traversable;

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
    private $LoadedRequestEvaluator;
    
    /**
     * @var boolean
     */
    private $IsLoaded = false;
    
    public function __construct()
    {
        
    }
    
    public function VisitValues(Requests\Values $Request)
    {
        if(!$this->IsLoaded) {
            $Traversable = new \Pinq\Traversable($this->LoadValues($Request));
            $this->LoadedRequestEvaluator = new Traversable\RequestEvaluator($Traversable);
            $this->IsLoaded = true;
        }
        
        return $this->LoadedRequestEvaluator->VisitValues($Request);
    }
    protected abstract function LoadValues(Requests\Values $Request);
    
    final public function VisitFirst(Requests\First $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitFirst($Request) : 
                $this->LoadFirst($Request);
    }
    protected abstract function LoadFirst(Requests\First $Request);
    
    final public function VisitLast(Requests\Last $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitLast($Request) : 
                $this->LoadLast($Request);
    }
    protected abstract function LoadLast(Requests\Last $Request);
    
    final public function VisitCount(Requests\Count $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitCount($Request) : 
                $this->LoadCount($Request);
    }
    protected abstract function LoadCount(Requests\Count $Request);
    
    final public function VisitExists(Requests\Exists $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitExists($Request) : 
                $this->LoadExists($Request);
    }
    protected abstract function LoadExists(Requests\Exists $Request);
    
    final public function VisitContains(Requests\Contains $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitContains($Request) : 
                $this->LoadContains($Request);
    }
    protected abstract function LoadContains(Requests\Contains $Request);
    
    final public function VisitAggregate(Requests\Aggregate $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitAggregate($Request) : 
                $this->LoadAggregate($Request);
    }
    protected abstract function LoadAggregate(Requests\Aggregate $Request);
    
    final public function VisitMaximum(Requests\Maximum $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitMaximum($Request) :
                $this->LoadMaximum($Request);
    }
    protected abstract function LoadMaximum(Requests\Maximum $Request);
    
    final public function VisitMinimum(Requests\Minimum $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitMinimum($Request) : 
                $this->LoadMinimum($Request);
    }
    protected abstract function LoadMinimum(Requests\Minimum $Request);
    
    final public function VisitGetIndex(Requests\GetIndex $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitGetIndex($Request) : 
                $this->LoadGetIndex($Request);
    }
    protected abstract function LoadGetIndex(Requests\GetIndex $Request);
    
    final public function VisitIssetIndex(Requests\IssetIndex $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitIssetIndex($Request) : 
                $this->LoadIssetIndex($Request);
    }
    protected abstract function LoadIssetIndex(Requests\IssetIndex $Request);
    
    final public function VisitSum(Requests\Sum $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitSum($Request) : 
                $this->LoadSum($Request);
    }
    protected abstract function LoadSum(Requests\Sum $Request);
    
    final public function VisitAverage(Requests\Average $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitAverage($Request) : 
                $this->LoadAverage($Request);
    }
    protected abstract function LoadAverage(Requests\Average $Request);
    
    final public function VisitAll(Requests\All $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitAll($Request) : 
                $this->LoadAll($Request);
    }
    protected abstract function LoadAll(Requests\All $Request);
    
    final public function VisitAny(Requests\Any $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitAny($Request) : 
                $this->LoadAny($Request);
    }
    protected abstract function LoadAny(Requests\Any $Request);
    
    final public function VisitImplode(Requests\Implode $Request)
    {
        return $this->IsLoaded ? 
                $this->LoadedRequestEvaluator->VisitImplode($Request) : 
                $this->LoadImplode($Request);
    }
    protected abstract function LoadImplode(Requests\Implode $Request);
}
