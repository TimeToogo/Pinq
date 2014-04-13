<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ThrowExpression extends Expression
{
    private $ExceptionExpression;

    public function __construct(Expression $ExceptionExpression)
    {
        $this->ExceptionExpression = $ExceptionExpression;
    }

    /**
     * @return Expression
     */
    public function GetExceptionExpression()
    {
        return $this->ExceptionExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkThrow($this);
    }

    public function Simplify()
    {
        return $this->Update($this->ExceptionExpression->Simplify());
    }

    /**
     * @return self
     */
    public function Update(Expression $ExceptionExpression)
    {
        if ($this->ExceptionExpression === $ExceptionExpression) {
            return $this;
        }

        return new self($ExceptionExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= 'throw ';
        $this->ExceptionExpression->CompileCode($Code);
    }
}
