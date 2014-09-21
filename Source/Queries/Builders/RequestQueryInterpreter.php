<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\PinqException;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\IRequestInterpretation;

class RequestQueryInterpreter extends QueryInterpreter implements IRequestQueryInterpreter
{
    /**
     * @var IRequestInterpretation
     */
    protected $interpretation;

    public function __construct(
            IRequestInterpretation $interpretation,
            IScopeInterpreter $scopeInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        parent::__construct('request', $scopeInterpreter, $evaluationContext);

        $this->interpretation = $interpretation;
    }

    public function getInterpretation()
    {
        return $this->interpretation;
    }

    public function interpret(O\Expression $expression)
    {
        if (($expression instanceof O\MethodCallExpression)
                && method_exists($this, $methodName = 'visit' . $this->getMethodName($expression))
        ) {
            $this->{$methodName}($expression);
        } elseif ($expression instanceof O\IndexExpression) {
            $this->{'visitOffsetGet'}($expression);
        } elseif ($expression instanceof O\IssetExpression) {
            $this->{'visitOffsetExists'}($expression);
        } elseif ($expression instanceof O\FunctionCallExpression
                && $expression->getName() instanceof O\ValueExpression
                && strtolower($expression->getName()->getValue()) === 'count'
        ) {
            $this->{'visitCount'}($expression);
        } else {
            $this->scopeInterpreter->interpretScope($expression);
        }
    }

    final protected function visitGetIterator(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretGetIterator($this->getId('get-iterator'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitGetTrueIterator(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretGetTrueIterator($this->getId('get-true-iterator'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAsArray(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAsArray($this->getId('as-array'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAsCollection(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAsCollection($this->getId('as-collection'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAsTraversable(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAsTraversable($this->getId('as-traversable'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitOffsetGet(O\Expression $expression)
    {
        $requestId = $this->getId('offset-get');
        $indexId   = $this->getId('offset-get-index');

        if ($expression instanceof O\MethodCallExpression) {
            $this->interpretation->interpretOffsetGet($requestId, $indexId, $this->getArgumentValueAt(0, $expression));
        } elseif ($expression instanceof O\IndexExpression) {
            $this->interpretation->interpretOffsetGet(
                    $requestId,
                    $indexId,
                    $this->getValue($expression->getIndex())
            );
        } else {
            throw new PinqException(
                    'Cannot interpret offset get request: invalid expression type, expecting %s, %s given',
                    O\MethodCallExpression::getType() . ' or ' . O\IndexExpression::getType(),
                    $expression->getType());
        }

        $this->interpretSourceAsScope($expression);
    }

    final protected function visitOffsetExists(O\Expression $expression)
    {
        $requestId = $this->getId('offset-exists');
        $indexId   = $this->getId('offset-exists-index');

        if ($expression instanceof O\MethodCallExpression) {
            $this->interpretation->interpretOffsetExists($requestId, $indexId, $this->getArgumentValueAt(0, $expression));
            $this->interpretSourceAsScope($expression);

            return;
        } elseif ($expression instanceof O\IssetExpression) {
            $issetArguments = $expression->getValues();

            if (count($issetArguments) === 1 && $issetArguments[0] instanceof O\IndexExpression) {
                $this->interpretation->interpretOffsetExists(
                        $requestId,
                        $indexId,
                        $this->getValue($issetArguments[0]->getIndex())
                );
                $this->interpretSourceAsScope($issetArguments[0]);

                return;
            }
        }

        throw new PinqException(
                'Cannot interpret offset exists request: invalid expression type, expecting %s, %s given',
                O\MethodCallExpression::getType() . ' or ' . O\IssetExpression::getType(
                ) . ' with a single parameter index',
                $expression->getType());
    }

    final protected function visitContains(O\MethodCallExpression $expression)
    {
        $requestId = $this->getId('contains');
        $valueId   = $this->getId('contains-value');

        $this->interpretation->interpretContains($requestId, $valueId, $this->getArgumentValueAt(0, $expression));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitFirst(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretFirst($this->getId('first'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitLast(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretLast($this->getId('last'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitCount(O\Expression $expression)
    {
        $this->interpretation->interpretCount($this->getId('count'));

        if ($expression instanceof O\MethodCallExpression) {
            $this->interpretSourceAsScope($expression);
        } elseif ($expression instanceof O\FunctionCallExpression
                && count($expression->getArguments()) > 0
        ) {
            $this->scopeInterpreter->interpretScope($expression->getArguments()[0]->getValue());
        } else {
            throw new PinqException(
                    'Cannot interpret count request: invalid expression type, expecting %s, %s given',
                    O\MethodCallExpression::getType() . ' or ' . O\FunctionCallExpression::getType(
                    ) . ' with at least one argument',
                    $expression->getType());
        }
    }

    final protected function visitIsEmpty(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretIsEmpty($this->getId('is-empty'));
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAggregate(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAggregate(
                $this->getId('aggregate'),
                $this->getFunctionAt('aggregate-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitMaximum(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretMaximum(
                $this->getId('maximum'),
                $this->getOptionalFunctionAt('maximum-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitMinimum(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretMinimum(
                $this->getId('minimum'),
                $this->getOptionalFunctionAt('minimum-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitSum(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretSum(
                $this->getId('sum'),
                $this->getOptionalFunctionAt('sum-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAverage(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAverage(
                $this->getId('average'),
                $this->getOptionalFunctionAt('average-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAll(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAll(
                $this->getId('all'),
                $this->getOptionalFunctionAt('all-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitAny(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretAny(
                $this->getId('any'),
                $this->getOptionalFunctionAt('any-function', 0, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }

    final protected function visitImplode(O\MethodCallExpression $expression)
    {
        $this->interpretation->interpretImplode(
                $this->getId('implode'),
                $this->getId('implode-delimiter'),
                $this->getArgumentValueAt(0, $expression),
                $this->getOptionalFunctionAt('implode-function', 1, $expression)
        );
        $this->interpretSourceAsScope($expression);
    }
}
