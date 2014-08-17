<?php

namespace Pinq\Queries\Segments;

use Pinq\Providers\DSL\IScopeCompiler;
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
        foreach ($scope->getSegments() as $query) {
            $query->traverse($this);
        }
    }

    public function visitOperation(Operation $query)
    {

    }

    public function visitRange(Range $query)
    {

    }

    public function visitUnique(Unique $query)
    {

    }

    public function visitFilter(Filter $query)
    {

    }

    public function visitGroupBy(GroupBy $query)
    {

    }

    public function visitJoin(Join $query)
    {

    }

    public function visitOrderBy(OrderBy $query)
    {

    }

    public function visitSelect(Select $query)
    {

    }

    public function visitSelectMany(SelectMany $query)
    {

    }

    public function visitIndexBy(IndexBy $query)
    {

    }

    public function visitKeys(Keys $query)
    {

    }

    public function visitReindex(Reindex $query)
    {

    }
}
