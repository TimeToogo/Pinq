<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Processors\Expression;
use Pinq\Queries\Functions\FunctionBase;
use Pinq\Queries;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class VariablePrefixorProcessor extends Expression\ExpressionProcessor
{
    /**
     * @var string
     */
    private $prefix;

    public function __construct($prefix, Queries\IScope $scope)
    {
        parent::__construct($scope);
        $this->prefix = $prefix;
    }

    public static function factory($prefix)
    {
        return function (Queries\IQuery $query) use ($prefix) {
            return Expression\ProcessorFactory::from($query, new self($prefix, $query->getScope()));
        };
    }

    public function forSubScope(Queries\IScope $scope)
    {
        return new self($this->prefix, $scope);
    }

    public function processFunction(FunctionBase $function)
    {
        $variablePrefixor = new O\DynamicExpressionWalker([
            O\VariableExpression::getType() =>
                    function (O\VariableExpression $expression) {
                        $name = $expression->getName();
                        if ($name instanceof O\ValueExpression) {
                            return $expression->update(
                                    O\Expression::value($this->prefix . $name->getValue())
                            );
                        } else {
                            return $expression->update(
                                    O\Expression::binaryOperation(
                                            O\Expression::value($this->prefix),
                                            O\Operators\Binary::CONCATENATION,
                                            $name
                                    )
                            );
                        }
                    }
        ]);

        $parameterScopeVariableMap = array_map(function ($variable) { return $this->prefix . $variable; }, $function->getParameterScopedVariableMap());

        return $function->update(
                $function->getScopeType(),
                $function->getNamespace(),
                $parameterScopeVariableMap,
                $variablePrefixor->walkAll($function->getParameters()->getAll()),
                $variablePrefixor->walkAll($function->getBodyExpressions()));
    }
}