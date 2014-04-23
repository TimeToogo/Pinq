<?php

namespace Pinq\Expressions\Walkers;

use Pinq\Expressions as O;
use Pinq\Expressions\Operators;

/**
 * Resolves and stores the expression of the return statements in an expression tree.
 *
 * $Var = 4 + 5 - $Unresolvable;
 * return 3 + $Var;
 * === will be resolved to ===
 * 3 + (4 + 5 - $Unresolvable)
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ReturnValueExpressionResolver extends O\ExpressionWalker
{
    /**
     * @var array<string, O\Expression>
     */
    private $variableExpressionMap = [];

    /**
     * @var VariableResolver
     */
    private $variableResolver;

    /**
     * @var O\Expression[]
     */
    private $returnValueExpressions = [];

    public function __construct()
    {
        $this->variableResolver = new VariableResolver();
    }

    /**
     * @return O\Expression[]
     */
    public function getResolvedReturnValueExpression()
    {
        return $this->returnValueExpressions;
    }

    public function resetReturnExpressions()
    {
        $this->variableExpressionMap = [];
        $this->returnValueExpressions = [];
    }

    private static $assignmentToBinaryOperator = [
        Operators\Assignment::ADDITION => Operators\Binary::ADDITION,
        Operators\Assignment::BITWISE_AND => Operators\Binary::BITWISE_AND,
        Operators\Assignment::BITWISE_OR => Operators\Binary::BITWISE_OR,
        Operators\Assignment::BITWISE_XOR => Operators\Binary::BITWISE_XOR,
        Operators\Assignment::CONCATENATE => Operators\Binary::CONCATENATION,
        Operators\Assignment::DIVISION => Operators\Binary::DIVISION,
        Operators\Assignment::MODULUS => Operators\Binary::MODULUS,
        Operators\Assignment::MULTIPLICATION => Operators\Binary::MULTIPLICATION,
        Operators\Assignment::SHIFT_LEFT => Operators\Binary::SHIFT_LEFT,
        Operators\Assignment::SHIFT_RIGHT => Operators\Binary::SHIFT_RIGHT,
        Operators\Assignment::SUBTRACTION => Operators\Binary::SUBTRACTION
    ];

    /**
     * @param string $assignmentOperator
     */
    private function assignmentToBinaryOperator($assignmentOperator)
    {
        return isset(self::$assignmentToBinaryOperator[$assignmentOperator]) ? self::$assignmentToBinaryOperator[$assignmentOperator] : null;
    }

    private function resolveVariables(O\Expression $expression)
    {
        $this->variableResolver->setVariableExpressionMap($this->variableExpressionMap);

        return $this->variableResolver->walk($expression);
    }

    /*
     * Convert any assignments to the equivalent binary expression and stores the value expression.
     */
    public function walkAssignment(O\AssignmentExpression $expression)
    {
        $assignToExpression = $this->walk($this->resolveVariables($expression->getAssignToExpression()))->simplify();
        $assignmentOperator = $expression->getOperator();
        $assignmentValueExpression = $this->walk($this->resolveVariables($expression->getAssignmentValueExpression()));

        if ($assignToExpression instanceof O\VariableExpression && $assignToExpression->getNameExpression() instanceof O\ValueExpression) {
            $assignmentName = $assignToExpression->getNameExpression()->getValue();
            $binaryOperator = $this->assignmentToBinaryOperator($assignmentOperator);

            if ($binaryOperator !== null) {
                $currentValueExpression = isset($this->variableExpressionMap[$assignmentName]) ? $this->variableExpressionMap[$assignmentName] : $assignToExpression;
                $variableValueExpression =
                        O\Expression::binaryOperation(
                                $currentValueExpression,
                                $binaryOperator,
                                $assignmentValueExpression);
            } else {
                $variableValueExpression = $assignmentValueExpression;
            }

            $this->variableExpressionMap[$assignmentName] = $variableValueExpression;
        }

        return $expression;
    }

    public function walkClosure(O\ClosureExpression $expression)
    {
        //Ignore closures
        return $expression;
    }

    public function walkReturn(O\ReturnExpression $returnExpression)
    {
        if ($returnExpression->hasValueExpression()) {
            $this->returnValueExpressions[] = $this->resolveVariables($returnExpression->getValueExpression())->simplify();
        } else {
            $this->returnValueExpressions[] = O\Expression::value(null);
        }

        return $returnExpression;
    }
}
