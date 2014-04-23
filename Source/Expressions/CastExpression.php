<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * (string)$I
 * </code>
 * 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CastExpression extends Expression
{
    /**
     * @var int
     */
    private $castType;
    
    /**
     * @var Expression
     */
    private $castValueExpression;
    
    public function __construct($castType, Expression $castValueExpression)
    {
        $this->castType = $castType;
        $this->castValueExpression = $castValueExpression;
    }
    
    /**
     * @return string The cast operator
     */
    public function getCastType()
    {
        return $this->castType;
    }
    
    /**
     * @return Expression The expression which is cast
     */
    public function getCastValueExpression()
    {
        return $this->castValueExpression;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkCast($this);
    }
    
    public function simplify()
    {
        $value = $this->castValueExpression->simplify();
        
        if ($value instanceof ValueExpression) {
            return Expression::value(self::castValue($this->castType, $value));
        }
        
        return $this->update($this->castType, $value);
    }
    
    private static $castTypeMap = [
        Operators\Cast::ARRAY_CAST => 'array',
        Operators\Cast::BOOLEAN => 'bool',
        Operators\Cast::DOUBLE => 'double',
        Operators\Cast::INTEGER => 'int',
        Operators\Cast::STRING => 'string',
        Operators\Cast::OBJECT => 'object'
    ];
    
    /**
     * @param ValueExpression $value
     */
    private static function castValue($castTypeOperator, $value)
    {
        settype($value, self::$castTypeMap[$castTypeOperator]);
        
        return $value;
    }
    
    /**
     * @return self
     */
    public function update($castType, Expression $castValueExpression)
    {
        if ($this->castType === $castType && $this->castValueExpression === $castValueExpression) {
            return $this;
        }
        
        return new self($castType, $castValueExpression);
    }
    
    protected function compileCode(&$code)
    {
        $code .= $this->castType;
        $this->castValueExpression->compileCode($code);
    }
    
    public function serialize()
    {
        return serialize([$this->castType, $this->castValueExpression]);
    }
    
    public function unserialize($serialized)
    {
        list($this->castType, $this->castValueExpression) = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->castValueExpression = clone $this->castValueExpression;
    }
}