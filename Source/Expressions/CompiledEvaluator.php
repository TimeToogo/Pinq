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
     * @var callable
     */
    protected $compiledEvaluator;

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
    return function ($$contextParameterName)
    {
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

    public function unserialize($serialized)
    {
        list($this->code, $this->context, $this->requiredVariables, $this->extraVariables) = unserialize($serialized);
        $this->initializeEvaluator();
    }

    private function initializeEvaluator()
    {
        //Note: must be eval'd in an instance context.
        //See: https://bugs.php.net/bug.php?id=65598
        $compiledEvaluator       = eval($this->code);
        $this->compiledEvaluator = $compiledEvaluator->bindTo(
                $this->context->getThis(),
                $this->context->getScopeType()
        );
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
        $evaluator = $this->compiledEvaluator;

        return $evaluator($variableTable + $this->extraVariables);
    }
}