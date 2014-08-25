<?php

namespace Pinq\Queries\Builders\Interpretations;

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
        return $this->request ?: new Requests\Values(Requests\Values::AS_SELF);
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
        $this->request = new Requests\GetIndex($indexId);
    }

    public function interpretOffsetExists($requestId, $indexId, $index)
    {
        $this->request = new Requests\IssetIndex($indexId);
    }

    public function interpretContains($requestId, $valueId, $value)
    {
        $this->request = new Requests\Contains($valueId);
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
                $this->buildFunction(
                        $function,
                        Queries\Functions\Aggregator::factory()
                ));
    }

    public function interpretMaximum($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Maximum($this->buildOptionalProjection($function));
    }

    protected function buildOptionalProjection(IFunction $function = null)
    {
        if ($function === null) {
            return null;
        }

        return $this->buildFunction(
                $function,
                Queries\Functions\ElementProjection::factory()
        );
    }

    public function interpretMinimum($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Minimum($this->buildOptionalProjection($function));
    }

    public function interpretSum($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Sum($this->buildOptionalProjection($function));
    }

    public function interpretAverage($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Average($this->buildOptionalProjection($function));
    }

    public function interpretAll($requestId, IFunction $function = null)
    {
        $this->request = new Requests\All($this->buildOptionalProjection($function));
    }

    public function interpretAny($requestId, IFunction $function = null)
    {
        $this->request = new Requests\Any($this->buildOptionalProjection($function));
    }

    public function interpretImplode($requestId, $delimiterId, $delimiter, IFunction $function = null)
    {
        $this->request = new Requests\Implode(
                $delimiterId,
                $this->buildOptionalProjection($function));
    }
}
