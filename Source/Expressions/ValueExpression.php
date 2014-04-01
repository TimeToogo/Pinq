<?php

namespace Pinq\Expressions;

/**
 * Expression representing a resolved value.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ValueExpression extends Expression
{
    private $Value;
    public function __construct($Value)
    {
        $this->Value = $Value;
    }

    /**
     * @return mixed The resolved value
     */
    public function GetValue()
    {
        return $this->Value;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkValue($this);
    }

    public function Simplify()
    {
        return $this;
    }

    /**
     * @return self
     */
    public function Update($Value)
    {
        if ($this->Value === $Value) {
            return $this;
        }

        return new self($Value);
    }

    protected function CompileCode(&$Code)
    {
        if(is_scalar($this->Value)
                || is_array($this->Value)
                || (is_object($this->Value) && method_exists($this->Value, '__set_state'))) {
            $Code .= var_export($this->Value, true);
        } 
        else if ($this->Value instanceof \Closure) {
            throw new \Pinq\PinqException('Cannot compile value expression: value of type Closure cannot be serialzed');
        } 
        else {
            $Code .= 'unserialize(\'' . serialize($this->Value) . '\')';
        }
    }
}
