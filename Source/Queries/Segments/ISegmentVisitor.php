<?php

namespace Pinq\Queries\Segments;

/**
 * Interface of the segment visitor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISegmentVisitor
{
    public function visitIndexBy(IndexBy $segment);

    public function visitSelect(Select $segment);

    public function visitKeys(Keys $segment);

    public function visitOperation(Operation $segment);

    public function visitRange(Range $segment);

    public function visitOrderBy(OrderBy $segment);

    public function visitGroupBy(GroupBy $segment);

    public function visitSelectMany(SelectMany $segment);

    public function visitFilter(Filter $segment);

    public function visitUnique(Unique $segment);

    public function visitJoin(Join $segment);

    public function visitReindex(Reindex $segment);
}
