<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ReturnExpression extends Expression
{
    private $ReturnValueExpression;

    public function __construct(Expression $ReturnValueExpression = null)
    {
        $this->ReturnValueExpression = $ReturnValueExpression;
    }

    /**
     * @return boolean
     */
    public function HasValueExpression()
    {
        return $this->ReturnValueExpression !== null;
    }

    /**
     * @return Expression|null
     */
    public function GetValueExpression()
    {
        return $this->ReturnValueExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkReturn($this);
    }

    public function Simplify()
    {
        return $this->Update($this->ReturnValueExpression->Simplify());
    }

    /**
     * @return self
     */
    public function Update(Expression $ReturnValueExpression = null)
    {
        if ($this->ReturnValueExpression === $ReturnValueExpression) {
            return $this;
        }

        return new self($ReturnValueExpression);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= 'return ';

        if ($this->ReturnValueExpression !== null) {
            $this->ReturnValueExpression->CompileCode($Code);
        }
    }
}
