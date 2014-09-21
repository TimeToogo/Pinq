<?php

namespace Pinq\Providers\Traversable;

use Pinq\ITraversable;
use Pinq\Queries;
use Pinq\Queries\Requests;

/**
 * Request evaluator for performing queries on the supplied traversable instance.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestEvaluator extends Requests\RequestVisitor
{
    /**
     * @var ITraversable
     */
    private $traversable;

    /**
     * @var Queries\IResolvedParameterRegistry
     */
    protected $resolvedParameters;

    public function __construct(
            ITraversable $traversable,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $this->traversable        = $traversable;
        $this->resolvedParameters = $resolvedParameters;
    }

    public static function evaluate(
            ITraversable $traversable,
            Queries\IRequest $request,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $evaluator = new self($traversable, $resolvedParameters);

        return $evaluator->visit($request);
    }

    /**
     * @return ITraversable
     */
    public function getTraversable()
    {
        return $this->traversable;
    }

    public function visitValues(Requests\Values $request)
    {
        switch ($request->getValuesType()) {
            case Requests\Values::AS_SELF:
                return $this->traversable;

            case Requests\Values::AS_ARRAY:
                return $this->traversable->asArray();

            case Requests\Values::AS_ARRAY_COMPATIBLE_ITERATOR:
                return $this->traversable->getIterator();

            case Requests\Values::AS_TRUE_ITERATOR:
                return $this->traversable->getTrueIterator();

            case Requests\Values::AS_TRAVERSABLE:
                return $this->traversable->asTraversable();

            case Requests\Values::AS_COLLECTION:
                return $this->traversable->asCollection();
        }
    }

    public function visitCount(Requests\Count $request)
    {
        return $this->traversable->count();
    }

    public function visitIsEmpty(Requests\IsEmpty $request)
    {
        return $this->traversable->isEmpty();
    }

    public function visitFirst(Requests\First $request)
    {
        return $this->traversable->first();
    }

    public function visitLast(Requests\Last $request)
    {
        return $this->traversable->last();
    }

    public function visitContains(Requests\Contains $request)
    {
        return $this->traversable->contains($this->resolvedParameters[$request->getValueId()]);
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        return $this->traversable[$this->resolvedParameters[$request->getIndexId()]];
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        return isset($this->traversable[$this->resolvedParameters[$request->getIndexId()]]);
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        return $this->traversable->aggregate(
                $this->resolvedParameters[$request->getAggregatorFunction()->getCallableId()]
        );
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        return $this->traversable->maximum($this->getOptionalFunction($request));
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        return $this->traversable->minimum($this->getOptionalFunction($request));
    }

    public function visitSum(Requests\Sum $request)
    {
        return $this->traversable->sum($this->getOptionalFunction($request));
    }

    public function visitAverage(Requests\Average $request)
    {
        return $this->traversable->average($this->getOptionalFunction($request));
    }

    public function visitAll(Requests\All $request)
    {
        return $this->traversable->all($this->getOptionalFunction($request));
    }

    public function visitAny(Requests\Any $request)
    {
        return $this->traversable->any($this->getOptionalFunction($request));
    }

    public function visitImplode(Requests\Implode $request)
    {
        return $this->traversable->implode(
                $this->resolvedParameters[$request->getDelimiterId()],
                $this->getOptionalFunction($request)
        );
    }

    private function getOptionalFunction(Requests\ProjectionRequestBase $request)
    {
        return $request->hasProjectionFunction() ? $this->resolvedParameters[$request->getProjectionFunction()
                ->getCallableId()] : null;
    }
}
