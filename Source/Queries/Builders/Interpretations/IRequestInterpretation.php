<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\Builders\Functions\IFunction;

/**
 * Interface for request interpretations.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestInterpretation
{
    public function interpretGetIterator($requestId);

    public function interpretGetTrueIterator($requestId);

    public function interpretAsArray($requestId);

    public function interpretAsCollection($requestId);

    public function interpretAsTraversable($requestId);

    public function interpretOffsetGet($requestId, $indexId, $index);

    public function interpretOffsetExists($requestId, $indexId, $index);

    public function interpretContains($requestId, $valueId, $value);

    public function interpretFirst($requestId);

    public function interpretLast($requestId);

    public function interpretCount($requestId);

    public function interpretIsEmpty($requestId);

    public function interpretAggregate($requestId, IFunction $function);

    public function interpretMaximum($requestId, IFunction $projection = null);

    public function interpretMinimum($requestId, IFunction $projection = null);

    public function interpretSum($requestId, IFunction $projection = null);

    public function interpretAverage($requestId, IFunction $projection = null);

    public function interpretAll($requestId, IFunction $projection = null);

    public function interpretAny($requestId, IFunction $projection = null);

    public function interpretImplode($requestId, $delimiterId, $delimiter, IFunction $projection = null);
}
