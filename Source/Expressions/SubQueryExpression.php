<?php

namespace Pinq\Expressions;

use \Pinq\Queries;

class QueryStreamSimplifier extends Queries\QueryStreamWalker 
{
    public function WalkFilter(Queries\Filter $Query)
    {
        return parent::WalkFilter($Query);
    }

    public function WalkGroupBy(Queries\GroupBy $Query)
    {
        return parent::WalkGroupBy($Query);
    }

    public function WalkIndexBy(Queries\IndexBy $Query)
    {
        return parent::WalkIndexBy($Query);
    }

    public function WalkOrderBy(Queries\OrderBy $Query)
    {
        return parent::WalkOrderBy($Query);
    }

    public function WalkSelect(Queries\Select $Query)
    {
        return parent::WalkSelect($Query);
    }

    public function WalkSelectMany(Queries\SelectMany $Query)
    {
        return parent::WalkSelectMany($Query);
    }

}

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
        foreach ($this->QueryStream->GetQueries() as $Query) {
            
        }
    }

    /**
     * @return self
     */
    public function Update(Expression $ValueExpression, Queries\IQueryStream $QueryStream)
    {
        if ($this->ValueExpression === $ValueExpression
                && $this->QueryStream === $QueryStream) {
            return $this;
        }

        return new self($ValueExpression, $QueryStream);
    }

    protected function UpdateValueExpression(Expression $ValueExpression)
    {
        return new self($ValueExpression, $this->QueryStream);
    }

    protected function CompileCode(&$Code)
    {
    }
}
