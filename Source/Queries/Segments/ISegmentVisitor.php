<?php

namespace Pinq\Queries\Segments;

/**
 * Interface of the segment visitor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISegmentVisitor
{
    public function visitIndexBy(IndexBy $query);

    public function visitSelect(Select $query);

    public function visitKeys(Keys $query);

    public function visitOperation(Operation $query);

    public function visitRange(Range $query);

    public function visitOrderBy(OrderBy $query);

    public function visitGroupBy(GroupBy $query);

    public function visitSelectMany(SelectMany $query);

    public function visitFilter(Filter $query);

    public function visitUnique(Unique $query);

    public function visitJoin(Join $query);

    public function visitReindex(Reindex $query);
}