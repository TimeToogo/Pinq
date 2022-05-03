<?php

namespace Pinq\Expressions;

use Pinq\Expressions\Operators\Assignment;

/**
 * <code>
 * $variable += 5
 * </code>
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AssignmentExpression extends Expression
{
    /**
     * @var Expression
     */
    private $assignTo;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var Expression
     */
    private $assignmentValue;

    public function __construct(Expression $assignTo, $operator, Expression $assignmentValue)
    {
        $this->assignTo        = $assignTo;
        $this->operator        = $operator;
        $this->assignmentValue = $assignmentValue;
    }

    /**
     * @return Expression
     */
    public function getAssignTo()
    {
        return $this->assignTo;
    }

    /**
     * @return string The assignment operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return Expression
     */
    public function getAssignmentValue()
    {
        return $this->assignmentValue;
    }

    /**
     * Converts the assignment expression with compound assignment operators
     * to the equivalent assignment and binary operator combination
     *
     * <code>
     * $variable += 5
     * </code>
     * becomes
     * <code>
     * $variable = $variable + 5
     * </code>
     *
     * @return AssignmentExpression
     */
    public function toBinaryOperationEquivalent()
    {
        $binaryOperator = Assignment::toBinaryOperator($this->operator);

        if($binaryOperator === null) {
            return $this;
        }

        return $this->update(
                $this->assignTo,
                Assignment::EQUAL,
                Expression::binaryOperation(
                        $this->assignTo,
                        $binaryOperator,
                        $this->assignmentValue
                )
        );
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkAssignment($this);
    }

    /**
     * @param Expression $assignTo
     * @param string     $operator
     * @param Expression $assignmentValue
     *
     * @return self
     */
    public function update(Expression $assignTo, $operator, Expression $assignmentValue)
    {
        if ($this->assignTo === $assignTo
                && $this->operator === $operator
                && $this->assignmentValue === $assignmentValue
        ) {
            return $this;
        }

        return new self($assignTo, $operator, $assignmentValue);
    }

    protected function compileCode(&$code)
    {
        $this->assignTo->compileCode($code);
        $code .= ' ' . $this->operator . ' ';
        $this->assignmentValue->compileCode($code);
    }

    public function serialize()
    {
        return serialize([$this->assignTo, $this->operator, $this->assignmentValue]);
    }
    
    public function __serialize(): array
    {
        return [$this->assignTo, $this->operator, $this->assignmentValue];
    }

    public function unserialize($serialized)
    {
        list($this->assignTo, $this->operator, $this->assignmentValue) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list($this->assignTo, $this->operator, $this->assignmentValue) = $data;
    }
    
    public function __clone()
    {
        $this->assignTo        = clone $this->assignTo;
        $this->assignmentValue = clone $this->assignmentValue;
    }
}
