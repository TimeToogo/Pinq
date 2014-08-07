<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Expressions as O;
use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Queries;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Requests;

/**
 * Implementation of the request interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestParser extends BaseParser implements IRequestParser
{
    /**
     * @var Queries\IRequest
     */
    protected $request;

    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    public function getRequest()
    {
        return $this->request ? : new Requests\Values(Requests\Values::AS_SELF);
    }

    public function interpretGetIterator($requestId)
    {
        $this->request = new Requests\Values(Requests\Values::AS_ARRAY_COMPATIBLE_ITERATOR);
    }

    public function interpretGetTrueIterator($requestId)
    {
        $this->request = new Requests\Values(Requests\Values::AS_TRUE_ITERATOR);
    }

    public function interpretAsArray($requestId)
    {
        $this->request = new Requests\Values(Requests\Values::AS_ARRAY);
    }

    public function interpretAsCollection($requestId)
    {
        $this->request = new Requests\Values(Requests\Values::AS_COLLECTION);
    }

    public function interpretAsTraversable($requestId)
    {
        $this->request = new Requests\Values(Requests\Values::AS_TRAVERSABLE);
    }

    public function interpretOffsetGet($requestId, $indexId, $index)
    {
        $this->request = new Requests\GetIndex($this->requireParameter($indexId));
    }

    public function interpretOffsetExists($requestId, $indexId, $index)
    {
        $this->request = new Requests\IssetIndex($this->requireParameter($indexId));
    }

    public function interpretContains($requestId, $valueId, $value)
    {
        $this->request = new Requests\Contains($this->requireParameter($valueId));
    }

    public function interpretFirst($requestId)
    {
        $this->request = new Requests\First();
    }

    public function interpretLast($requestId)
    {
        $this->request = new Requests\Last();
    }

    public function interpretCount($requestId)
    {
        $this->request = new Requests\Count();
    }

    public function interpretIsEmpty($requestId)
    {
        $this->request = new Requests\IsEmpty();
    }

    public function interpretAggregate($requestId, IFunction $function)
    {
        $this->request = new Requests\Aggregate(
                $this->requireFunction(
                        $function,
                        Queries\Functions\Aggregator::factory()
                ));
    }

    public function interpretMaximum($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Maximum($this->requireOptionalProjection($function));
    }

    protected function requireOptionalProjection(IFunction $function = null)
    {
        if ($function === null) {
            return null;
        }

        return $this->requireFunction(
                $function,
                Queries\Functions\ElementProjection::factory()
        );
    }

    public function interpretMinimum($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Minimum($this->requireOptionalProjection($function));
    }

    public function interpretSum($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Sum($this->requireOptionalProjection($function));
    }

    public function interpretAverage($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Average($this->requireOptionalProjection($function));
    }

    public function interpretAll($requestId, IFunction $function = null)
    {
        $this->request = new Requests\All($this->requireOptionalProjection($function));
    }

    public function interpretAny($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Any($this->requireOptionalProjection($function));
    }

    public function interpretImplode($requestId, $delimiterId, $delimiter, IFunction $function = null)
    {
        $this->request = new Requests\Implode(
                $this->requireParameter($delimiterId),
                $this->requireOptionalProjection($function));
    }
}
