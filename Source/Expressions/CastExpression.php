<?php

namespace Pinq\Expressions;

/**
 * Expression representing a cast operation.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CastExpression extends Expression
{
    private $CastType;
    private $CastValueExpression;

    public function __construct($CastType, Expression $CastValueExpression)
    {
        $this->CastType = $CastType;
        $this->CastValueExpression = $CastValueExpression;
    }

    /**
     * @return string The cast operator
     */
    public function GetCastType()
    {
        return $this->CastType;
    }

    /**
     * @return Expression The expression which is cast
     */
    public function GetCastValueExpression()
    {
        return $this->CastValueExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkCast($this);
    }

    public function Simplify()
    {
        $Value = $this->CastValueExpression->Simplify();
        if ($Value instanceof ValueExpression) {
            return Expression::Value(self::CastValue($this->CastType, $Value));
        }

        return $this->Update(
                $this->CastType,
                $Value);
    }

    private static $CastTypeMap = [
        Operators\Cast::ArrayCast => 'array',
        Operators\Cast::Boolean => 'bool',
        Operators\Cast::Double => 'double',
        Operators\Cast::Integer => 'int',
        Operators\Cast::String => 'string',
        Operators\Cast::Object => 'object',
    ];
    private static function CastValue($CastTypeOperator, $Value)
    {
        settype($Value, self::$CastTypeMap[$CastTypeOperator]);

        return $Value;
    }

    /**
     * @return self
     */
    public function Update($CastType, Expression $CastValueExpression)
    {
        if ($this->CastType === $CastType
                && $this->CastValueExpression === $CastValueExpression) {
            return $this;
        }

        return new self($CastType, $CastValueExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= $this->CastType;
        $this->CastValueExpression->CompileCode($Code);
    }
}
