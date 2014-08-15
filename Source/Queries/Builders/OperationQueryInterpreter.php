<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\IOperationInterpretation;
use Pinq\Queries\Common;

class OperationQueryInterpreter extends QueryInterpreter implements IOperationQueryInterpreter
{
    /**
     * @var IOperationInterpretation
     */
    protected $interpretation;

    public function __construct(
            IOperationInterpretation $interpretation,
            IScopeInterpreter $scopeInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        parent::__construct('operation', $scopeInterpreter, $evaluationContext);

        $this->interpretation = $interpretation;
    }

    public function getInterpretation()
    {
        return $this->interpretation;
    }

    public function interpret(O\Expression $expression)
    {
        if ($expression instanceof O\MethodCallExpression) {
            $this->{'visit' . $this->getMethodName($expression)}($expression);
        } elseif ($expression instanceof O\AssignmentExpression
                && $expression->getAssignTo() instanceof O\IndexExpression
        ) {
            $this->{'visitOffsetSet'}($expression);
        } elseif ($expression instanceof O\UnsetExpression) {
            $this->{'visitOffsetUnset'}($expression);
        } else {
            $this->scopeInterpreter->interpretScope($expression);
        }
    }

    final protected function visitApply(O\MethodCallExpression $expression)
    {
        $sourceExpression = $expression->getValue();

        //Determine whether this was a join/groupJoin apply operation
        if ($sourceExpression instanceof O\MethodCallExpression) {
            $methodName = $this->getMethodName($sourceExpression);
            if (in_array(strtolower($methodName), ['withdefault', 'on', 'onequality', 'join', 'groupjoin'], true)) {
                $this->visitJoinApply($expression);
                return;
            }
        }

        $this->interpretation->interpretApply('apply', $this->getFunctionAt('apply-function', 0, $expression));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitJoinApply(O\MethodCallExpression $expression)
    {
        $applyFunction = $this->getFunctionAt($this->getId('apply-function'), 0, $expression);
        $expression = $this->getSourceMethodCall($expression);
        $optionsInterpreter = $this->scopeInterpreter->buildJoinOptionsInterpreter($this->getId('join-apply'));
        $optionsInterpreter->interpretJoinOptions($expression, $sourceExpression);
        $this->interpretation->interpretJoinApply(
                $this->getId('join-apply'),
                $optionsInterpreter->getInterpretation(),
                $applyFunction
        );

        $this->interpretSourceAsScope($sourceExpression);
    }

    final protected function visitAddRange(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAddRange(
                $this->getId('add-range'),
                $this->interpretSource($this->getId('add-range-source'), $this->getArgumentAt(0, $expression))
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function interpretSource($id, O\Expression $expression)
    {
        $sourceInterpreter = $this->scopeInterpreter->buildSourceInterpreter($id);
        $sourceInterpreter->interpretSource($expression);

        return $sourceInterpreter->getInterpretation();
    }

    final protected function visitRemoveRange(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretRemoveRange(
                $this->getId('remove-range'),
                $this->interpretSource($this->getId('remove-range-source'), $this->getArgumentAt(0, $expression))
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitRemove(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretRemove(
                $this->getId('remove'),
                $this->getId('remove-value'),
                $this->getArgumentValueAt(0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitRemoveWhere(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretRemoveWhere(
                $this->getId('remove-where'),
                $this->getFunctionAt($this->getId('remove-where-projection'), 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitClear(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretClear($this->getId('clear'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitOffsetSet(O\Expression $expression)
    {
        $operationId = $this->getId('offset-set');
        $indexId     = $this->getId('set-index');
        $valueId     = $this->getId('set-value');
        if ($expression instanceof O\MethodCallExpression) {
            $this->interpretation->interpretOffsetSet(
                    $operationId,
                    $indexId,
                    $this->getArgumentValueAt(0, $expression),
                    $valueId,
                    $this->getArgumentValueAt(1, $expression)
            );
            $this->interpretSourceAsScope($expression);
            return;
        } elseif ($expression instanceof O\AssignmentExpression) {
            $assignTo = $expression->getAssignTo();

            if ($assignTo instanceof O\IndexExpression) {
                $this->interpretation->interpretOffsetSet(
                        $operationId,
                        $indexId,
                        $this->getValue($assignTo->getIndex()),
                        $valueId,
                        $this->getValue($expression->getAssignmentValue())
                );
                $this->interpretSourceAsScope($assignTo);
                return;
            }
        }

        throw new \Pinq\PinqException(
                'Cannot interpret offset set operation: invalid expression type, expecting %s, %s given',
                O\MethodCallExpression::getType() . ' or ' . O\AssignmentExpression::getType(),
                $expression->getType());
    }

    final protected function visitOffsetUnset(O\Expression $expression)
    {
        $operationId = $this->getId('offset-unset');
        $indexId     = $this->getId('unset-index');
        if ($expression instanceof O\MethodCallExpression) {
            $this->interpretation->interpretOffsetUnset(
                    $operationId,
                    $indexId,
                    $this->getArgumentValueAt(0, $expression)
            );
            $this->interpretSourceAsScope($expression);
            return;
        } elseif ($expression instanceof O\UnsetExpression) {
            $unsetArguments = $expression->getValues();

            if (count($unsetArguments) === 1 && $unsetArguments[0] instanceof O\IndexExpression) {
                $this->interpretation->interpretOffsetUnset(
                        $operationId,
                        $indexId,
                        $this->getValue($unsetArguments[0]->getIndex(), 'index value must be constant, %s given')
                );
                $this->interpretSourceAsScope($unsetArguments[0]);
                return;
            }
        }

        throw new \Pinq\PinqException(
                'Cannot interpret offset unset operation: invalid expression type, expecting %s, %s given',
                O\MethodCallExpression::getType() . ' or ' . O\IssetExpression::getType(
                ) . ' with a single parameter index',
                $expression->getType());
    }
}
