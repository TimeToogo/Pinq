<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation;
use Pinq\Queries\Requests;
use Pinq\Queries;

class RequestCompiler extends Compilation\RequestCompiler
{
    /**
     * @var CompiledQuery
     */
    protected $compilation;

    public function visitValues(Requests\Values $request)
    {
        $this->compilation->append('Get the elements as ');
        switch ($request->getValuesType()) {
            case Requests\Values::AS_ARRAY:
                $this->compilation->append('an array');
                break;

            case Requests\Values::AS_ARRAY_COMPATIBLE_ITERATOR:
                $this->compilation->append('an array compatible iterator');
                break;

            case Requests\Values::AS_TRUE_ITERATOR:
                $this->compilation->append('an iterator');
                break;

            case Requests\Values::AS_SELF:
                $this->compilation->append('itself');
                break;

            case Requests\Values::AS_TRAVERSABLE:
                $this->compilation->append('a traversable');
                break;

            case Requests\Values::AS_COLLECTION:
                $this->compilation->append('a collection');
                break;
        }
    }

    public function visitCount(Requests\Count $request)
    {
        $this->compilation->append('Get the amount of elements');
    }

    public function visitIsEmpty(Requests\IsEmpty $request)
    {
        $this->compilation->append('Get whether there are no elements');
    }

    public function visitFirst(Requests\First $request)
    {
        $this->compilation->append('Get the first element');
    }

    public function visitLast(Requests\Last $request)
    {
        $this->compilation->append('Get the last element');
    }

    public function visitContains(Requests\Contains $request)
    {
        $this->compilation->append('Get if contains the parameter');
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        $this->compilation->append('Get the values aggregated according to the function: ');
        $this->compilation->appendFunction($request->getAggregatorFunction());
    }

    protected function appendOptionalProjection($string, Requests\ProjectionRequest $request)
    {
        $this->compilation->append($string);
        if ($request->hasProjectionFunction()) {
            $this->compilation->append(' according to the function: ');
            $this->compilation->appendFunction($request->getProjectionFunction());
        }
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        $this->appendOptionalProjection('Get the maximum value', $request);
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        $this->appendOptionalProjection('Get the minimum value', $request);
    }

    public function visitSum(Requests\Sum $request)
    {
        $this->appendOptionalProjection('Get the sum of the values', $request);
    }

    public function visitAverage(Requests\Average $request)
    {
        $this->appendOptionalProjection('Get the average of the values', $request);
    }

    public function visitAll(Requests\All $request)
    {
        $this->appendOptionalProjection('Whether all of the values are truthy', $request);
    }

    public function visitAny(Requests\Any $request)
    {
        $this->appendOptionalProjection('Whether any of the values are truthy', $request);
    }

    public function visitImplode(Requests\Implode $request)
    {
        $this->appendOptionalProjection('Get as a delimited string', $request);
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        $this->compilation->append('Get the index');
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        $this->compilation->append('Get whether the index is set');
    }

}
