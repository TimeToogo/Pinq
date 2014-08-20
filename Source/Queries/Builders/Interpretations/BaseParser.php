<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Queries\Builders\Functions\CallableFunction;
use Pinq\Queries\Builders\Functions\ClosureExpressionFunction;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries;

/**
 * Base class for query expression parsing.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BaseParser extends BaseInterpretation implements IQueryParser
{
    /**
     * @var string[]
     */
    protected $parameters = [];

    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        parent::__construct($functionInterpreter);
    }

    /**
     * @return string[]
     */
    public function getRequiredParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Queries\IParameterRegistry
     */
    protected function buildRequirements()
    {
        return new Queries\ParameterRegistry($this->parameters);
    }

    final protected function requireFunction(IFunction $function, callable $factory)
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
            $scopeType            = $function->getEvaluationContext()->getScopeType();
            $namespace            = $function->getEvaluationContext()->getNamespace();
            $expression           = $function->getExpression();
            $parameterExpressions = $expression->getParameters();
            $bodyExpressions      = $expression->getBodyExpressions();
            $scopedVariableNames  = array_merge(['this'], $expression->getUsedVariableNames());
        } else {
            throw new \Pinq\PinqException(
                    'Cannot require function parameter: unsupported function type, %s',
                    get_class($function));
        }

        return $factory(
                $this->requireParameter($this->getFunctionCallableParameter($function)),
                $scopeType,
                $namespace,
                $this->requireFunctionScope($function, $scopedVariableNames),
                $parameterExpressions,
                $bodyExpressions
        );
    }

    final protected function requireParameter($name)
    {
        if (isset($this->parameters[$name])) {
            throw new \Pinq\PinqException('Cannot require parameter %s: parameter name is already required', $name);
        }

        $this->parameters[$name] = $name;

        return $name;
    }

    final protected function requireFunctionScope(IFunction $function, array $scopedVariableNames)
    {
        $parameterScopedVariableMap = [];

        foreach (array_unique($scopedVariableNames) as $scopedVariable) {
            $parameterScopedVariableMap[$this->requireParameter(
                    $this->getFunctionScopedVariableParameter($function, $scopedVariable)
            )] = $scopedVariable;
        }

        return $parameterScopedVariableMap;
    }

    final protected function requireParametersFrom(IQueryParser $parser)
    {
        foreach ($parser->getRequiredParameters() as $name) {
            $this->requireParameter($name);
        }
    }

    final protected function requireSource(ISourceParser $sourceParser)
    {
        $this->requireParametersFrom($sourceParser);

        return $sourceParser->getSource();
    }

    final protected function requireJoinOptions(IJoinOptionsParser $joinOptionsParser)
    {
        $this->requireParametersFrom($joinOptionsParser);

        return $joinOptionsParser->getJoinOptions();
    }
}
