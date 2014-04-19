<?php

namespace Pinq\Expressions;

use \Pinq\Queries;
use \Pinq\Queries\Segments;

class ScopeSimplifier extends Segments\SegmentWalker 
{
    public function WalkFilter(Segments\Filter $Query)
    {
        return $Query->Update($Query->GetFunctionExpressionTree()->Simplify());
    }

    public function WalkGroupBy(Segments\GroupBy $Query)
    {
        return $Query->Update(array_map(function ($I) { return $I->Simplify(); }, $Query->GetFunctionExpressionTrees()));
    }

    public function WalkIndexBy(Segments\IndexBy $Query)
    {
        return $Query->Update($Query->GetFunctionExpressionTree()->Simplify());
    }

    public function WalkOrderBy(Segments\OrderBy $Query)
    {
        return $Query->Update(
                array_map(function ($I) { return $I->Simplify(); }, $Query->GetFunctionExpressionTrees()),
                $Query->GetIsAscendingArray());
    }

    public function WalkSelect(Segments\Select $Query)
    {
        return $Query->Update($Query->GetFunctionExpressionTree()->Simplify());
    }

    public function WalkSelectMany(Segments\SelectMany $Query)
    {
        return $Query->Update($Query->GetFunctionExpressionTree()->Simplify());
    }
}

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SubQueryExpression extends TraversalExpression
{
    /**
     * @var Queries\IRequestQuery
     */
    private $Query;

    public function __construct(Expression $ValueExpression, Queries\IRequestQuery $Query)
    {
        parent::__construct($ValueExpression);

        $this->Query = $Query;
    }

    /**
     * @return Queries\IRequestQuery
     */
    public function GetQueryStream()
    {
        return $this->Query;
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
    public function Update(Expression $ValueExpression,  Queries\IRequestQuery $Query)
    {
        if ($this->ValueExpression === $ValueExpression
                && $this->Query === $Query) {
            return $this;
        }

        return new self($ValueExpression, $Query);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->Query);
    }

    protected function CompileCode(&$Code)
    {
    }
    
    public function serialize()
    {
        return serialize([$this->ValueExpression, $this->Query]);
    }
    
    public function unserialize($Serialized)
    {
        list($this->ValueExpression, $this->Query) = unserialize($Serialized);
    }
        
    public function __clone()
    {
        $this->ValueExpression = clone $this->ValueExpression;
        $this->Query = clone $this->Query;
    }
}
