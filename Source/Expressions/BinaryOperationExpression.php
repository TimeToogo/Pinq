<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * $One + $Two;
 * $Five . 'foo';
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class BinaryOperationExpression extends Expression
{
    /**
     * @var Expression
     */
    private $leftOperandExpression;
    
    /**
     * @var int
     */
    private $operator;
    
    /**
     * @var Expression
     */
    private $rightOperandExpression;
    
    public function __construct(Expression $leftOperandExpression, $operator, Expression $rightOperandExpression)
    {
        $this->leftOperandExpression = $leftOperandExpression;
        $this->operator = $operator;
        $this->rightOperandExpression = $rightOperandExpression;
    }
    
    /**
     * @return string The binary operator
     */
    public function getOperator()
    {
        return $this->operator;
    }
    
    /**
     * @return Expression
     */
    public function getLeftOperandExpression()
    {
        return $this->leftOperandExpression;
    }
    
    /**
     * @return Expression
     */
    public function getRightOperandExpression()
    {
        return $this->rightOperandExpression;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkBinaryOperation($this);
    }
    
    public function simplify()
    {
        $left = $this->leftOperandExpression->simplify();
        $right = $this->rightOperandExpression->simplify();
        
        if ($left instanceof ValueExpression && $right instanceof ValueExpression) {
            return Expression::value(self::doBinaryOperation(
                    $left->getValue(),
                    $this->operator,
                    $right->getValue()));
        }
        else if ($left instanceof ValueExpression || $right instanceof ValueExpression) {
            $valueExpression = $left instanceof ValueExpression ? $left : $right;
            $otherExpression = $left instanceof ValueExpression ? $right : $left;
            $value = $valueExpression->getValue();
            
            if ($this->operator === Operators\Binary::LOGICAL_OR && $value == true) {
                return Expression::value(true);
            }
            else if ($this->operator === Operators\Binary::LOGICAL_OR && $value == false) {
                return $otherExpression;
            }
            else if ($this->operator === Operators\Binary::LOGICAL_AND && $value == false) {
                return Expression::value(false);
            }
            else if ($this->operator === Operators\Binary::LOGICAL_AND && $value == true) {
                return $otherExpression;
            }
        }
        
        return $this->update($left, $this->operator, $right);
    }
    
    private static $binaryOperations;
    
    private static function doBinaryOperation($left, $operator, $right)
    {
        if (self::$binaryOperations === null) {
            self::$binaryOperations = [
                Operators\Binary::BITWISE_AND => function ($l, $r) { return $l & $r; },
                Operators\Binary::BITWISE_OR => function ($l, $r) { return $l | $r; },
                Operators\Binary::BITWISE_XOR => function ($l, $r) { return $l ^ $r; },
                Operators\Binary::SHIFT_LEFT => function ($l, $r) { return $l << $r; },
                Operators\Binary::SHIFT_RIGHT => function ($l, $r) { return $l >> $r; },
                Operators\Binary::LOGICAL_AND => function ($l, $r) { return $l && $r; },
                Operators\Binary::LOGICAL_OR => function ($l, $r) { return $l || $r; },
                Operators\Binary::ADDITION => function ($l, $r) { return $l + $r; },
                Operators\Binary::SUBTRACTION => function ($l, $r) { return $l - $r; },
                Operators\Binary::MULTIPLICATION => function ($l, $r) { return $l * $r; },
                Operators\Binary::DIVISION => function ($l, $r) { return $l / $r; },
                Operators\Binary::MODULUS => function ($l, $r) { return $l % $r; },
                Operators\Binary::CONCATENATION => function ($l, $r) { return $l . $r; },
                Operators\Binary::IS_INSTANCE_OF => function ($l, $r) { return $l instanceof $r; },
                Operators\Binary::EQUALITY => function ($l, $r) { return $l == $r; },
                Operators\Binary::IDENTITY => function ($l, $r) { return $l === $r; },
                Operators\Binary::INEQUALITY => function ($l, $r) { return $l != $r; },
                Operators\Binary::NOT_IDENTICAL => function ($l, $r) { return $l !== $r; },
                Operators\Binary::LESS_THAN => function ($l, $r) { return $l < $r; },
                Operators\Binary::LESS_THAN_OR_EQUAL_TO => function ($l, $r) { return $l <= $r; },
                Operators\Binary::GREATER_THAN => function ($l, $r) { return $l > $r; },
                Operators\Binary::GREATER_THAN_OR_EQUAL_TO => function ($l, $r) { return $l >= $r; }
            ];
        }
        
        $operation = self::$binaryOperations[$operator];
        
        return $operation($left, $right);
    }
    
    /**
     * @return self
     */
    public function update(Expression $leftOperandExpression, $operator, Expression $rightOperandExpression)
    {
        if ($this->leftOperandExpression === $leftOperandExpression && $this->operator === $operator && $this->rightOperandExpression === $rightOperandExpression) {
            return $this;
        }
        
        return new self($leftOperandExpression, $operator, $rightOperandExpression);
    }
    
    protected function compileCode(&$code)
    {
        $code .= '(';
        $this->leftOperandExpression->compileCode($code);
        $code .= ' ' . $this->operator . ' ';
        $this->rightOperandExpression->compileCode($code);
        $code .= ')';
    }
    
    public function serialize()
    {
        return serialize([$this->leftOperandExpression, $this->operator, $this->rightOperandExpression]);
    }
    
    public function unserialize($serialized)
    {
        list($this->leftOperandExpression, $this->operator, $this->rightOperandExpression) = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->leftOperandExpression = clone $this->leftOperandExpression;
        $this->rightOperandExpression = clone $this->rightOperandExpression;
    }
}