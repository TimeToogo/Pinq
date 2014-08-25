<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Queries;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Functions;

/**
 * Implementation of the operation resolver.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationResolver extends BaseResolver implements IOperationResolver
{
    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    public function interpretApply($operationId, IFunction $function)
    {
        $this->appendToHash($operationId);
        $this->resolveFunction($function);
    }

    public function interpretJoinApply(
            $operationId,
            IJoinOptionsInterpretation $joinOptionsInterpretation,
            IFunction $applyFunction
    ) {
        /** @var $joinOptionsInterpretation IJoinOptionsResolver */
        $this->appendToHash($operationId);
        $this->resolveParametersFrom($joinOptionsInterpretation);
        $this->resolveFunction($applyFunction);
    }

    public function interpretAddRange($operationId, ISourceInterpretation $sourceInterpretation)
    {
        /** @var $sourceInterpretation ISourceResolver */
        $this->appendToHash($operationId);
        $this->resolveParametersFrom($sourceInterpretation);
    }

    public function interpretRemoveRange($operationId, ISourceInterpretation $sourceInterpretation)
    {
        /** @var $sourceInterpretation ISourceResolver */
        $this->appendToHash($operationId);
        $this->resolveParametersFrom($sourceInterpretation);
    }

    public function interpretRemoveWhere($operationId, IFunction $function)
    {
        $this->appendToHash($operationId);
        $this->resolveFunction($function);
    }

    public function interpretClear($operationId)
    {
        $this->appendToHash($operationId);
    }

    public function interpretOffsetSet($operationId, $indexId, $index, $valueId, $value)
    {
        $this->appendToHash($operationId);
        $this->resolveParameter($indexId, $index);
        $this->resolveParameter($valueId, $value);
    }

    public function interpretOffsetUnset($operationId, $indexId, $index)
    {
        $this->appendToHash($operationId);
        $this->resolveParameter($indexId, $index);
    }
}
