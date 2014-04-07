<?php

namespace Pinq\Providers\Traversable;

use \Pinq\Queries\Requests;

class RequestEvaluator extends Requests\RequestVisitor
{
    /**
     * @var \Pinq\ITraversable
     */
    private $Traversable;
    
    public function __construct(\Pinq\ITraversable $Traversable)
    {
        $this->Traversable = $Traversable;
    }

    public function VisitValues(Requests\Values $Request)
    {
        return $this->Traversable->getIterator();
    }

    public function VisitCount(Requests\Count $Request)
    {
        return $this->Traversable->Count();
    }

    public function VisitExists(Requests\Exists $Request)
    {
        return $this->Traversable->Exists();
    }

    public function VisitFirst(Requests\First $Request)
    {
        return $this->Traversable->First();
    }

    public function VisitLast(Requests\Last $Request)
    {
        return $this->Traversable->Last();
    }

    public function VisitContains(Requests\Contains $Request)
    {
        return $this->Traversable->Contains($Request->GetValue());
    }

    public function VisitGetIndex(Requests\GetIndex $Request)
    {
        return $this->Traversable[$Request->GetIndex()];
    }

    public function VisitIssetIndex(Requests\IssetIndex $Request)
    {
        return isset($this->Traversable[$Request->GetIndex()]);
    }
    
    public function VisitAggregate(Requests\Aggregate $Request)
    {
        return $this->Traversable->Aggregate($Request->GetFunctionExpressionTree());
    }

    public function VisitMaximum(Requests\Maximum $Request)
    {
        return $this->Traversable->Maximum($Request->GetFunctionExpressionTree());
    }

    public function VisitMinimum(Requests\Minimum $Request)
    {
        return $this->Traversable->Minimum($Request->GetFunctionExpressionTree());
    }

    public function VisitSum(Requests\Sum $Request)
    {
        return $this->Traversable->Sum($Request->GetFunctionExpressionTree());
    }

    public function VisitAverage(Requests\Average $Request)
    {
        return $this->Traversable->Average($Request->GetFunctionExpressionTree());
    }

    public function VisitAll(Requests\All $Request)
    {
        return $this->Traversable->All($Request->GetFunctionExpressionTree());
    }

    public function VisitAny(Requests\Any $Request)
    {
        return $this->Traversable->Any($Request->GetFunctionExpressionTree());
    }

    public function VisitImplode(Requests\Implode $Request)
    {
        return $this->Traversable->Implode(
                $Request->GetDelimiter(),
                $Request->GetFunctionExpressionTree());
    }
}
