<?php

namespace Pinq\Queries\Segments;

/**
 * The segment visitor is a utility class that will visit any
 * segment in a respective method.
 *
 * This is used by the query providers to as to evaluate the
 * scope of the query with the specified query segments
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SegmentVisitor extends SegmentWalker
{
    final public function walkOperation(Operation $query)
    {
        $this->visitOperation($query);

        return $query;
    }

    public function visitOperation(Operation $query)
    {

    }

    final public function walkRange(Range $query)
    {
        $this->visitRange($query);

        return $query;
    }

    public function visitRange(Range $query)
    {

    }

    final public function walkUnique(Unique $query)
    {
        $this->visitUnique($query);

        return $query;
    }

    public function visitUnique(Unique $query)
    {

    }

    final public function walkFilter(Filter $query)
    {
        $this->visitFilter($query);

        return $query;
    }

    public function visitFilter(Filter $query)
    {

    }

    final public function walkGroupBy(GroupBy $query)
    {
        $this->visitGroupBy($query);

        return $query;
    }

    public function visitGroupBy(GroupBy $query)
    {

    }

    final public function walkJoin(Join $query)
    {
        $this->visitJoin($query);

        return $query;
    }

    public function visitJoin(Join $query)
    {

    }

    final public function walkEqualityJoin(EqualityJoin $query)
    {
        $this->visitEqualityJoin($query);

        return $query;
    }

    public function visitEqualityJoin(EqualityJoin $query)
    {

    }

    final public function walkOrderBy(OrderBy $query)
    {
        $this->visitOrderBy($query);

        return $query;
    }

    public function visitOrderBy(OrderBy $query)
    {

    }

    final public function walkSelect(Select $query)
    {
        $this->visitSelect($query);

        return $query;
    }

    public function visitSelect(Select $query)
    {

    }

    final public function walkSelectMany(SelectMany $query)
    {
        $this->visitSelectMany($query);

        return $query;
    }

    public function visitSelectMany(SelectMany $query)
    {

    }

    final public function walkIndexBy(IndexBy $query)
    {
        $this->visitIndexBy($query);

        return $query;
    }

    protected function visitIndexBy(IndexBy $query)
    {

    }

    public function walkKeys(Keys $query)
    {
        $this->visitKeys($query);

        return $query;
    }

    protected function visitKeys(Keys $query)
    {

    }

    public function walkReindex(Reindex $query)
    {
        $this->visitReindex($query);

        return $query;
    }

    protected function visitReindex(Reindex $query)
    {

    }
}
