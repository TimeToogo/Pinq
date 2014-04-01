<?php

namespace Pinq\Expressions;

use \Pinq\Queries;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SubQueryExpression extends TraversalExpression
{
    /**
     * @var Queries\IQeuryStream
     */
    private $QueryStream;

    public function __construct(Expression $ValueExpression, Queries\IQueryStream $QueryStream)
    {
        parent::__construct($ValueExpression);

        $this->QueryStream = $QueryStream;
    }

    /**
     * @return Queries\IQeuryStream
     */
    public function GetQueryStream()
    {
        return $this->QueryStream;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkQueryStream($this);
    }

    public function Simplify()
    {
        foreach ($this->QueryStream->GetStream() as $Query) {

        }
    }

    /**
     * @return self
     */
    public function Update(Expression $NameExpression, array $ArgumentExpressions = [])
    {
        if ($this->ValueExpression === $NameExpression
                && $this->QueryStream === $ArgumentExpressions) {
            return $this;
        }

        return new self($NameExpression, $ArgumentExpressions);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        
    }

    protected function CompileCode(&$Code)
    {
    }
}
