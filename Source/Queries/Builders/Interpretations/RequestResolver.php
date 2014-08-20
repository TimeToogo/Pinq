<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries;

/**
 * Implementation of the request resolver.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RequestResolver extends BaseResolver implements IRequestResolver
{
    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    public function interpretGetIterator($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretGetTrueIterator($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretAsArray($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretAsCollection($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretAsTraversable($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretOffsetGet($requestId, $indexId, $index)
    {
        $this->appendToHash($requestId);
        $this->resolveParameter($indexId, $index);
    }

    public function interpretOffsetExists($requestId, $indexId, $index)
    {
        $this->appendToHash($requestId);
        $this->resolveParameter($indexId, $index);
    }

    public function interpretContains($requestId, $valueId, $value)
    {
        $this->appendToHash($requestId);
        $this->resolveParameter($valueId, $value);
    }

    public function interpretFirst($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretLast($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretCount($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretIsEmpty($requestId)
    {
        $this->appendToHash($requestId);
    }

    public function interpretAggregate($requestId, IFunction $function)
    {
        $this->appendToHash($requestId);
        $this->resolveFunction($function);
    }

    public function interpretMaximum($requestId, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveOptionalProjection($function);
    }

    protected function resolveOptionalProjection(IFunction $function = null)
    {
        if ($function !== null) {
            $this->resolveFunction($function);
        }
    }

    public function interpretMinimum($requestId, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveOptionalProjection($function);
    }

    public function interpretSum($requestId, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveOptionalProjection($function);
    }

    public function interpretAverage($requestId, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveOptionalProjection($function);
    }

    public function interpretAll($requestId, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveOptionalProjection($function);
    }

    public function interpretAny($requestId, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveOptionalProjection($function);
    }

    public function interpretImplode($requestId, $delimiterId, $delimiter, IFunction $function = null)
    {
        $this->appendToHash($requestId);
        $this->resolveParameter($delimiterId, $delimiter);
        $this->resolveOptionalProjection($function);
    }
}
