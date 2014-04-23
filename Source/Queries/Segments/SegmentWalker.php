<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\IScope;

/**
 * The segment walker is a utility class that will walk any
 * segment in a respective method. This can be implemented such
 * that it visitor or manipulates a query scope
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SegmentWalker
{
    /**
     * @param IScope $scope
     * @return IScope
     */
    final public function walk(IScope $scope)
    {
        $queries = $scope->getSegments();

        foreach ($queries as $key => $query) {
            $queries[$key] = $query->traverse($this);
        }

        return $scope->update($queries);
    }

    public function walkOperation(Operation $query)
    {
        return $query;
    }

    public function walkRange(Range $query)
    {
        return $query;
    }

    public function walkUnique(Unique $query)
    {
        return $query;
    }

    public function walkFilter(Filter $query)
    {
        return $query;
    }

    public function walkGroupBy(GroupBy $query)
    {
        return $query;
    }

    public function walkJoin(Join $join)
    {
        return $join;
    }

    public function walkEqualityJoin(EqualityJoin $join)
    {
        return $join;
    }

    public function walkOrderBy(OrderBy $query)
    {
        return $query;
    }

    public function walkSelect(Select $query)
    {
        return $query;
    }

    public function walkSelectMany(SelectMany $query)
    {
        return $query;
    }

    public function walkIndexBy(IndexBy $query)
    {
        return $query;
    }
}
