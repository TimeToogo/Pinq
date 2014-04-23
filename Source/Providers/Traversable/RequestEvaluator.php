<?php

namespace Pinq\Providers\Traversable;

use Pinq\Queries\Requests;

/**
 * Request evaluator for performing queries on the supplied traversable instance.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RequestEvaluator extends Requests\RequestVisitor
{
    /**
     * @var \Pinq\ITraversable
     */
    private $traversable;

    public function __construct(\Pinq\ITraversable $traversable)
    {
        $this->traversable = $traversable;
    }

    public function visitValues(Requests\Values $request)
    {
        return $this->traversable->getIterator();
    }

    public function visitCount(Requests\Count $request)
    {
        return $this->traversable->count();
    }

    public function visitExists(Requests\Exists $request)
    {
        return $this->traversable->exists();
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
        return $this->traversable->contains($request->getValue());
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        return $this->traversable[$request->getIndex()];
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        return isset($this->traversable[$request->getIndex()]);
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        return $this->traversable->aggregate($request->getFunctionExpressionTree());
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        return $this->traversable->maximum($request->getFunctionExpressionTree());
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        return $this->traversable->minimum($request->getFunctionExpressionTree());
    }

    public function visitSum(Requests\Sum $request)
    {
        return $this->traversable->sum($request->getFunctionExpressionTree());
    }

    public function visitAverage(Requests\Average $request)
    {
        return $this->traversable->average($request->getFunctionExpressionTree());
    }

    public function visitAll(Requests\All $request)
    {
        return $this->traversable->all($request->getFunctionExpressionTree());
    }

    public function visitAny(Requests\Any $request)
    {
        return $this->traversable->any($request->getFunctionExpressionTree());
    }

    public function visitImplode(Requests\Implode $request)
    {
        return $this->traversable->implode(
                $request->getDelimiter(),
                $request->getFunctionExpressionTree());
    }
}
