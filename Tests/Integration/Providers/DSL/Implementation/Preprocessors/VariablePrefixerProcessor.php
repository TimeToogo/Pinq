<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors;

use Pinq\Expressions as O;
use Pinq\Expressions\VariableExpression;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries;
use Pinq\Queries\Functions\IFunction;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class VariablePrefixerProcessor extends Expression\ExpressionProcessor
{
    /**
     * @var string
     */
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public static function factory($prefix)
    {
        return function (Queries\IQuery $query) use ($prefix) {
            return Expression\ProcessorFactory::from($query, new self($prefix));
        };
    }

    public function walkVariable(VariableExpression $expression)
    {
        $name = $expression->getName();
        if ($name instanceof O\ValueExpression) {
            return $expression->update(
                    O\Expression::value($this->prefix . $name->getValue())
            );
        }

        return $expression->update(
                O\Expression::binaryOperation(
                        O\Expression::value($this->prefix),
                        O\Operators\Binary::CONCATENATION,
                        $name
                )
        );
    }

    public function processFunction(IFunction $function)
    {
        $parameterScopeVariableMap = array_map(function ($variable) { return $this->prefix . $variable; }, $function->getParameterScopedVariableMap());

        return $function->update(
                $function->getScopeType(),
                $function->getNamespace(),
                $parameterScopeVariableMap,
                $this->walkAll($function->getParameters()->getAll()),
                $this->walkAll($function->getBodyExpressions())
        );
    }
}