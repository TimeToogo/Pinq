<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\PinqException;
use Pinq\Queries;
use Pinq\Queries\Builders\Functions\CallableFunction;
use Pinq\Queries\Builders\Functions\ClosureExpressionFunction;
use Pinq\Queries\Builders\Functions\IFunction;

/**
 * Base class for query expression parsing.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BaseParser extends BaseInterpretation implements IQueryParser
{
    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    final protected function buildFunction(IFunction $function, callable $factory)
    {
        if ($function instanceof CallableFunction) {
            $reflection           = $this->functionInterpreter->getReflection($function->getCallable());
            $scopeType            = $reflection->getScope()->getScopeType();
            $namespace            = $reflection->getInnerReflection()->getNamespaceName() ?: null;
            $parameterExpressions = $reflection->getSignature()->getParameterExpressions();
            $scopedVariableNames  = $reflection->getSignature()->isStatic() ? [] : ['this'];
            $scopedVariableNames  = array_merge(
                    $scopedVariableNames,
                    $reflection->getSignature()->getScopedVariableNames() ?: []
            );
            $bodyExpressions      = $reflection->getInnerReflection()->isUserDefined()
                    ? $this->functionInterpreter->getStructure($reflection)->getBodyExpressions() : null;
        } elseif ($function instanceof ClosureExpressionFunction) {
            if ($function->hasEvaluationContext()) {
                $scopeType = $function->getEvaluationContext()->getScopeType();
                $namespace = $function->getEvaluationContext()->getNamespace();
            } else {
                $scopeType = null;
                $namespace = null;
            }
            $expression           = $function->getExpression();
            $parameterExpressions = $expression->getParameters();
            $bodyExpressions      = $expression->getBodyExpressions();
            $scopedVariableNames  = array_merge(['this'], $expression->getUsedVariableNames());
        } else {
            throw new PinqException(
                    'Cannot build function: unsupported function type, %s',
                    get_class($function));
        }

        return $factory(
                $this->getFunctionCallableParameter($function),
                $scopeType,
                $namespace,
                $this->getFunctionScopeParameterMap($function, $scopedVariableNames),
                $parameterExpressions,
                $bodyExpressions
        );
    }

    final protected function getFunctionScopeParameterMap(IFunction $function, array $scopedVariableNames)
    {
        $parameterScopedVariableMap = [];

        foreach (array_unique($scopedVariableNames) as $scopedVariable) {
            $parameterScopedVariableMap[$this->getFunctionScopedVariableParameter(
                    $function,
                    $scopedVariable
            )] = $scopedVariable;
        }

        return $parameterScopedVariableMap;
    }
}
