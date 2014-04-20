<?php

namespace Pinq\Expressions;

use \Pinq\Queries;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SubQueryExpression extends TraversalExpression
{
    /**
     * @var Queries\IRequestQuery
     */
    private $Query;
    
    /**
     * @var TraversalExpression
     */
    private $OriginalExpression;

    public function __construct(Expression $ValueExpression, Queries\IRequestQuery $Query, TraversalExpression $OriginalExpression)
    {
        parent::__construct($ValueExpression);

        $this->Query = $Query;
        $this->OriginalExpression = $OriginalExpression;
    }

    /**
     * @return Queries\IRequestQuery
     */
    public function GetRequestQuery()
    {
        return $this->Query;
    }

    /**
     * @return TraversalExpression
     */
    public function GetOriginalExpression()
    {
        return $this->OriginalExpression;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkSubQuery($this);
    }

    public function Simplify()
    {
        return $this->UpdateValueExpression($this->ValueExpression->Simplify());
    }

    /**
     * @return self
     */
    public function Update(Expression $ValueExpression,  Queries\IRequestQuery $Query, TraversalExpression $OriginalExpression)
    {
        if ($this->ValueExpression === $ValueExpression
                && $this->Query === $Query
                && $this->OriginalExpression === $OriginalExpression) {
            return $this;
        }

        return new self($ValueExpression, $Query, $OriginalExpression);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->Query, $this->OriginalExpression);
    }

    protected function CompileCode(&$Code)
    {
        $this->OriginalExpression->CompileCode($Code);
    }
    
    public function DataToSerialize()
    {
        return [$this->Query, $this->OriginalExpression];
    }
    
    public function UnserializedData($Data)
    {
        list($this->Query, $this->OriginalExpression) = $Data;
    }
        
    public function __clone()
    {
        $this->ValueExpression = clone $this->ValueExpression;
        $this->Query = clone $this->Query;
        $this->OriginalExpression = clone $this->OriginalExpression;
    }
}
