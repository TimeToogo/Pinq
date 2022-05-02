<?php

namespace Pinq\Expressions;

/**
 * Implementation of the expression evaluator using compiled closures.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CompiledEvaluator extends Evaluator implements \Serializable
{
    const CONTEXT_PARAMETER_NAME = '__CONTEXT__';

    /**
     * @var string
     */
    protected $code;

    /**
     * @var \Closure
     */
    protected $originalCompiledEvaluator;

    /**
     * @var \Closure
     */
    protected $boundCompiledEvaluator;

    /**
     * @var mixed[]
     */
    protected $extraVariables = [];

    /**
     * Creates a new compiled evaluator from the supplied expressions.
     *
     * @param Expression[]       $expressions
     * @param IEvaluationContext $context
     *
     * @return CompiledEvaluator
     */
    public static function fromExpressions(array $expressions, IEvaluationContext $context = null)
    {
        $evaluator            = new self($context);
        $namespace            = $evaluator->context->getNamespace();
        $contextParameterName = self::CONTEXT_PARAMETER_NAME;
        $variableTable        = $evaluator->context->getVariableTable();

        //Replace any non value types with references in the variable table.
        $expressions = (new DynamicExpressionWalker([
                ValueExpression::getType() =>
                        function (ValueExpression $expression) use ($evaluator, $variableTable, &$name) {
                            $value = $expression->getValue();
                            if (!ValueExpression::isValueType($value)) {
                                $name = $name !== null ? $name : 0;
                                do {
                                    $name++;
                                } while (isset($variableTable['o' . $name]));

                                $name                             = 'o' . $name;
                                $evaluator->extraVariables[$name] = $value;

                                return Expression::variable(Expression::value($name));
                            }

                            return $expression;
                        }
        ]))->walkAll($expressions);

        $bodyCode = '';
        foreach ($evaluator->extraVariables + $variableTable as $variable => $value) {
            $variableName = Expression::value($variable);
            $variableCode = Expression::variable($variableName)->compile();
            $bodyCode .= "$variableCode =& $$contextParameterName" . '[' . $variableName->compile() . '];';
        }

        $bodyCode .= "unset($$contextParameterName);";
        $bodyCode .= implode(';', Expression::compileAll($expressions)) . ';';
        $evaluator->code = <<<PHP
namespace {$namespace} {
    return function ($$contextParameterName) {
        $bodyCode
    };
}
PHP;

        $evaluator->initializeEvaluator();

        return $evaluator;
    }

    public function serialize()
    {
        return serialize([$this->code, $this->context, $this->requiredVariables, $this->extraVariables]);
    }

    public function __serialize(): array
    {
        return [$this->code, $this->context, $this->requiredVariables, $this->extraVariables];
    }

    public function unserialize($serialized)
    {
        list($this->code, $this->context, $this->requiredVariables, $this->extraVariables) = unserialize($serialized);
        $this->initializeEvaluator();
    }

    public function __unserialize(array $data): void
    {
        list($this->code, $this->context, $this->requiredVariables, $this->extraVariables) = $data;
        $this->initializeEvaluator();
    }

    private function initializeEvaluator()
    {
        $this->originalCompiledEvaluator = self::evalCode($this->code);
        $this->boundCompiledEvaluator    = $this->originalCompiledEvaluator->bindTo(
                $this->context->getThis(),
                $this->context->getScopeType()
        );
    }

    private static function evalCode(string $code): \Closure
    {
        return eval($code);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    protected function doEvaluation(array $variableTable)
    {
        $evaluator = $this->boundCompiledEvaluator;

        return $evaluator($variableTable + $this->extraVariables);
    }

    protected function doEvaluationWithNewThis(array $variableTable, $newThis)
    {
        $evaluator = $this->originalCompiledEvaluator->bindTo($newThis, $this->context->getScopeType());

        return $evaluator($variableTable + $this->extraVariables);
    }
}
