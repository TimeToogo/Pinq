<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Functions;
use Pinq\Queries\Operations;
use Pinq\Queries;

/**
 * Implementation of the operation parser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationParser extends BaseParser implements IOperationParser
{
    /**
     * @var Queries\IOperation
     */
    protected $operation;

    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function interpretApply($operationId, IFunction $function)
    {
        $this->operation = new Operations\Apply(
                $this->buildFunction(
                        $function,
                        Functions\ElementMutator::factory()
                ));
    }

    public function interpretJoinApply(
            $operationId,
            IJoinOptionsInterpretation $joinOptionsInterpretation,
            IFunction $applyFunction
    ) {
        /** @var $joinOptionsInterpretation IJoinOptionsParser */
        $this->operation = new Operations\JoinApply(
                $joinOptionsInterpretation->getJoinOptions(),
                $this->buildFunction($applyFunction, Functions\ConnectorMutator::factory()));
    }

    public function interpretAddRange($operationId, ISourceInterpretation $sourceInterpretation)
    {
        /** @var $sourceInterpretation ISourceParser */
        $this->operation = new Operations\AddValues($sourceInterpretation->getSource());
    }

    public function interpretRemoveRange($operationId, ISourceInterpretation $sourceInterpretation)
    {
        /** @var $sourceInterpretation ISourceParser */
        $this->operation = new Operations\RemoveValues($sourceInterpretation->getSource());
    }

    public function interpretRemoveWhere($operationId, IFunction $function)
    {
        $this->operation = new Operations\RemoveWhere(
                $this->buildFunction(
                        $function,
                        Functions\ElementProjection::factory()
                ));
    }

    public function interpretClear($operationId)
    {
        $this->operation = new Operations\Clear();
    }

    public function interpretOffsetSet($operationId, $indexId, $index, $valueId, $value)
    {
        $this->operation = new Operations\SetIndex($indexId, $valueId);
    }

    public function interpretOffsetUnset($operationId, $indexId, $index)
    {
        $this->operation = new Operations\UnsetIndex($indexId);
    }
}
