<?php

namespace Pinq\Expressions;

use Pinq\PinqException;

/**
 * Implementation of the expression evaluator using compiled closures.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Evaluator implements IEvaluator
{
    /**
     * @var IEvaluationContext
     */
    protected $context;

    /**
     * @var string[]
     */
    protected $requiredVariables;

    protected function __construct(IEvaluationContext $context = null)
    {
        $this->context = $context ?: EvaluationContext::globalScope();
        $this->requiredVariables = array_keys($this->context->getVariableTable());
    }

    final public function getContext()
    {
        return $this->context;
    }

    final public function getRequiredVariables()
    {
        return $this->requiredVariables;
    }

    public function evaluate(array $variableTable = null)
    {
        return $this->doEvaluation($this->getVariableTable($variableTable));
    }

    public function evaluateWithNewThis($thisObject, array $variableTable = null)
    {
        return $this->doEvaluationWithNewThis($this->getVariableTable($variableTable), $thisObject);
    }

    protected function getVariableTable(array $customVariableTable = null)
    {
        //Loose equality: order is irrelevant
        if ($customVariableTable !== null && count(array_diff(array_keys($customVariableTable), $this->requiredVariables)) > 0) {
            throw new PinqException(
                    'Cannot evaluate expression: supplied variable table is invalid, variable names do not match the required variable names');
        }

        $contextVariableTable = $this->context->getVariableTable() ?: [];
        $customVariableTable = $customVariableTable ?: [];

        return $customVariableTable + $contextVariableTable;
    }

    abstract protected function doEvaluation(array $variableTable);

    abstract protected function doEvaluationWithNewThis(array $variableTable, $newThis);
}
