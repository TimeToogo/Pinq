<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Providers\DSL\Compilation\Processors\Visitors;
use Pinq\Queries\Requests;
use Pinq\Queries;

/**
 * Implementation of the request query processor to update function
 * expression trees of the query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestQueryProcessor extends Visitors\RequestQueryProcessor
{
    /**
     * @var ScopeProcessor
     */
    protected $scopeProcessor;

    /**
     * @var IExpressionProcessor
     */
    protected $expressionProcessor;

    public function __construct(IExpressionProcessor $expressionProcessor, Queries\IRequestQuery $requestQuery)
    {
        parent::__construct(new ScopeProcessor($requestQuery->getScope(), $expressionProcessor), $requestQuery);

        $this->expressionProcessor = $expressionProcessor;
    }

    protected function updateOptionalProjection(Requests\ProjectionRequestBase $projectionRequest)
    {
        if ($projectionRequest->hasProjectionFunction()) {
            return $this->expressionProcessor->processFunction($projectionRequest->getProjectionFunction());
        }

        return $projectionRequest;
    }

    public function visitAverage(Requests\Average $request)
    {
        return parent::visitAverage($this->updateOptionalProjection($request));
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        return parent::visitMinimum($this->updateOptionalProjection($request));
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        return parent::visitMaximum($this->updateOptionalProjection($request));
    }

    public function visitAll(Requests\All $request)
    {
        return parent::visitAll($this->updateOptionalProjection($request));
    }

    public function visitSum(Requests\Sum $request)
    {
        return parent::visitSum($this->updateOptionalProjection($request));
    }

    public function visitAny(Requests\Any $request)
    {
        return parent::visitAny($this->updateOptionalProjection($request));
    }

    public function visitImplode(Requests\Implode $request)
    {
        return parent::visitImplode($this->updateOptionalProjection($request));
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        return parent::visitAggregate(
                $request->update($this->expressionProcessor->processFunction($request->getAggregatorFunction()))
        );
    }
}
