<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\IJoinOptionsInterpretation;

/**
 * Implementation of the scope expression interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoinOptionsInterpreter extends ExpressionInterpreter implements IJoinOptionsInterpreter
{
    /**
     * @var IJoinOptionsInterpretation
     */
    protected $interpretation;

    /**
     * @var ISourceInterpreter
     */
    protected $sourceInterpreter;

    public function __construct(
            $segmentId,
            IJoinOptionsInterpretation $interpretation,
            ISourceInterpreter $sourceInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        parent::__construct($segmentId, $evaluationContext);
        $this->interpretation    = $interpretation;
        $this->sourceInterpreter = $sourceInterpreter;
    }

    public function getInterpretation()
    {
        return $this->interpretation;
    }

    public function interpretJoinOptions(
            O\MethodCallExpression $expression,
            O\MethodCallExpression &$sourceExpression = null
    ) {
        $defaultValueKeyPair = null;
        while (strcasecmp($this->getMethodName($expression), 'withDefault') === 0) {
            if ($defaultValueKeyPair === null) {
                $defaultValueKeyPair =
                        [$this->getArgumentValueAt(0, $expression), $this->getOptionalArgumentValueAt(1, $expression)];
            }

            $expression = $this->getSourceMethodCall($expression);
        }

        switch (strtolower($this->getMethodName($expression))) {
            case 'on':
                $this->interpretation->interpretCustomJoinFilter(
                        $this->getFunctionAt($this->getId('filter'), 0, $expression)
                );
                $expression = $this->getSourceMethodCall($expression);
                break;
            case 'onequality':
                $this->interpretation->interpretEqualityJoinFilter(
                        $this->getFunctionAt($this->getId('outer-key'), 0, $expression),
                        $this->getFunctionAt($this->getId('inner-key'), 1, $expression)
                );
                $expression = $this->getSourceMethodCall($expression);
                break;
        }

        $this->sourceInterpreter->interpretSource($this->getArgumentAt(0, $expression));
        $sourceInterpretation =  $this->sourceInterpreter->getInterpretation();
        $isGroupJoin      = strcasecmp($this->getMethodName($expression), 'groupJoin') === 0;
        $sourceExpression = $expression;

        return $this->interpretation->interpretJoinOptions(
                $isGroupJoin,
                $sourceInterpretation,
                $defaultValueKeyPair !== null ? $this->getId('default-key') : null,
                $defaultValueKeyPair !== null ? $this->getId('default-value') : null,
                $defaultValueKeyPair
        );
    }
}
