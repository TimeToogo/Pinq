<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Parsing;
use Pinq\PinqException;
use Pinq\Queries\Builders\Functions\CallableFunction;
use Pinq\Queries\Builders\Functions\ClosureExpressionFunction;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries;

class BaseResolver extends BaseInterpretation implements IQueryResolver
{
    /**
     * @var string
     */
    protected $hash = '';

    /**
     * @var array<string, mixed>
     */
    protected $resolvedParameters = [];

    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getResolvedParameters()
    {
        return $this->resolvedParameters;
    }

    final protected function appendToHash($value)
    {
        $this->hash .= $value;
    }

    final protected function resolveFunction(IFunction $function)
    {
        $callable = $function->getCallable();
        $this->resolveParameter($this->getFunctionCallableParameter($function), $function->getCallable());

        if ($function instanceof CallableFunction) {
            $reflection = $this->functionInterpreter->getReflection($callable);
            $this->resolveFunctionScope($function, $reflection);
        } elseif ($function instanceof ClosureExpressionFunction) {
            $expression = $function->getExpression();
            $this->hash .= $expression->hash();
            if ($function->hasEvaluationContext()) {
                $evaluationContext = $function->getEvaluationContext();
                $this->resolveParameter(
                        $this->getFunctionScopedVariableParameter($function, 'this'),
                        $evaluationContext->getThis()
                );

                foreach (array_intersect_key(
                                 $evaluationContext->getVariableTable(),
                                 array_flip($expression->getUsedVariableNames())
                         ) as $variableName => $value) {
                    $this->resolveParameter($this->getFunctionScopedVariableParameter($function, $variableName), $value);
                }
            }
        } else {
            throw new PinqException(
                    'Cannot resolve function: unsupported function type %s',
                    get_class($function));
        }
    }

    final protected function resolveParameter($name, $resolvedValue)
    {
        if (array_key_exists($name, $this->resolvedParameters)) {
            throw new PinqException(
                    'Cannot resolve parameter %s: parameter has already been resolved.',
                    $name);
        }

        $this->hash .= $name;

        $this->resolvedParameters[$name] = $resolvedValue;
    }

    final protected function resolveFunctionScope(IFunction $function, Parsing\IFunctionReflection $reflection)
    {
        $this->hash .= $reflection->getGlobalHash();

        if (!$reflection->getSignature()->isStatic()) {
            $this->resolveParameter(
                    $this->getFunctionScopedVariableParameter($function, 'this'),
                    $reflection->getScope()->getThis()
            );
        }

        $variableValueMap = $reflection->getScope()->getVariableTable();
        foreach ($variableValueMap as $variableName => $value) {
            $this->resolveParameter($this->getFunctionScopedVariableParameter($function, $variableName), $value);
        }
    }

    final protected function resolveParametersFrom(IQueryResolver $resolver)
    {
        $this->hash .= $resolver->getHash();

        foreach ($resolver->getResolvedParameters() as $name => $value) {
            $this->resolveParameter($name, $value);
        }
    }
}
