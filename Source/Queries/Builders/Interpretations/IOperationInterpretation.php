<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\Builders\Functions\IFunction;

/**
 * Interface for operation interpretations.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationInterpretation
{
    public function interpretApply($operationId, IFunction $function);

    public function interpretJoinApply(
            $operationId,
            IJoinOptionsInterpretation $joinOptionsInterpretation,
            IFunction $applyFunction
    );

    public function interpretAddRange($operationId, ISourceInterpretation $sourceInterpretation);

    public function interpretRemoveRange($operationId, ISourceInterpretation $sourceInterpretation);

    public function interpretRemoveWhere($operationId, IFunction $function);

    public function interpretClear($operationId);

    public function interpretOffsetSet($operationId, $indexId, $index, $valueId, $value);

    public function interpretOffsetUnset($operationId, $indexId, $index);
}
