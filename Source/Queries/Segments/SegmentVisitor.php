<?php

namespace Pinq\Queries\Segments;

class SegmentVisitor extends SegmentWalker
{
    final public function WalkOperation(Operation $Query)
    {
        $this->VisitOperation($Query);
        return $Query;
    }
    public function VisitOperation(Operation $Query) {}

    final public function WalkRange(Range $Query)
    {
        $this->VisitRange($Query);
        return $Query;
    }
    public function VisitRange(Range $Query) {}

    final public function WalkUnique(Unique $Query)
    {
        $this->VisitUnique($Query);
        return $Query;
    }
    public function VisitUnique(Unique $Query) {}
    
    final public function WalkFilter(Filter $Query)
    {
        $this->VisitFilter($Query);
        return $Query;
    }
    public function VisitFilter(Filter $Query) {}

    final public function WalkGroupBy(GroupBy $Query)
    {
        $this->VisitGroupBy($Query);
        return $Query;
    }
    public function VisitGroupBy(GroupBy $Query) {}

    final public function WalkJoin(Join $Query)
    {
        $this->VisitJoin($Query);
        return $Query;
    }
    public function VisitJoin(Join $Query) {}
    
    final public function WalkEqualityJoin(EqualityJoin $Query)
    {
        $this->VisitEqualityJoin($Query);
        return $Query;
    }
    public function VisitEqualityJoin(EqualityJoin $Query) {}

    final public function WalkOrderBy(OrderBy $Query)
    {
        $this->VisitOrderBy($Query);
        return $Query;
    }
    public function VisitOrderBy(OrderBy $Query) {}

    final public function WalkSelect(Select $Query)
    {
        $this->VisitSelect($Query);
        return $Query;
    }
    public function VisitSelect(Select $Query) {}

    final public function WalkSelectMany(SelectMany $Query)
    {
        $this->VisitSelectMany($Query);
        return $Query;
    }
    public function VisitSelectMany(SelectMany $Query) {}

    final public function WalkIndexBy(IndexBy $Query)
    {
        $this->VisitIndexBy($Query);
        return $Query;
    }
    protected function VisitIndexBy(IndexBy $Query) {}
}
