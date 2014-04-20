<?php

namespace Pinq\Providers\Caching;

use \Pinq\Queries;
use \Pinq\Queries\Requests;

class RequestEvaluator extends Requests\RequestVisitor
{
    /**
     * @var Requests\RequestVisitor
     */
    private $InnerRequestEvaluator;
    
    /**
     * @var array
     */
    private $MethodResultCache = [];
    
    public function __construct(Requests\RequestVisitor $InnerRequestEvaluator)
    {
        $this->InnerRequestEvaluator = $InnerRequestEvaluator;
    }
    
    private function CacheMethodResult($MethodName, Queries\IRequest $Request) 
    {
        $Key = $MethodName . '-' . md5(serialize($Request));
        
        if(!isset($this->MethodResultCache[$Key])) {
            $this->MethodResultCache[$Key] = $this->InnerRequestEvaluator->$MethodName($Request);
        }

        return $this->MethodResultCache[$Key];
    }
    
    public function VisitValues(Requests\Values $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitCount(Requests\Count $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitExists(Requests\Exists $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitFirst(Requests\First $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitLast(Requests\Last $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitContains(Requests\Contains $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }
    
    public function VisitGetIndex(Requests\GetIndex $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }
    
    public function VisitIssetIndex(Requests\IssetIndex $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }
    
    public function VisitAggregate(Requests\Aggregate $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitMaximum(Requests\Maximum $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitMinimum(Requests\Minimum $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitSum(Requests\Sum $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitAverage(Requests\Average $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitAll(Requests\All $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitAny(Requests\Any $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }

    public function VisitImplode(Requests\Implode $Request)
    {
        return $this->CacheMethodResult(__FUNCTION__, $Request);
    }
}
