<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\IScope;

/**
 * The segment visitor traverses through the segments in the supplied
 * query scope.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SegmentVisitor implements ISegmentVisitor
{
    /**
     * @param IScope $scope
     *
     * @return IScope
     */
    final public function visit(IScope $scope)
    {
        $scope->visit($this);
    }

    public function visitOperation(Operation $segment)
    {

    }

    public function visitRange(Range $segment)
    {

    }

    public function visitUnique(Unique $segment)
    {

    }

    public function visitFilter(Filter $segment)
    {

    }

    public function visitGroupBy(GroupBy $segment)
    {

    }

    public function visitJoin(Join $segment)
    {

    }

    public function visitOrderBy(OrderBy $segment)
    {

    }

    public function visitSelect(Select $segment)
    {

    }

    public function visitSelectMany(SelectMany $segment)
    {

    }

    public function visitIndexBy(IndexBy $segment)
    {

    }

    public function visitKeys(Keys $segment)
    {

    }

    public function visitReindex(Reindex $segment)
    {

    }
}
