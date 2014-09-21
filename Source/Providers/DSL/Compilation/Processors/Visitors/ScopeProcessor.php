<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Visitors;

use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\Segments;

/**
 * Implementation of the scope processor using the visitor pattern.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeProcessor extends Processors\ScopeProcessor implements Segments\ISegmentVisitor
{
    /**
     * @var Queries\ISegment[]
     */
    private $segments;

    protected function processSegments(array $segments)
    {
        $this->segments = [];
        foreach ($segments as $segment) {
            $segment->traverse($this);
        }

        return $this->segments;
    }

    public function visitIndexBy(Segments\IndexBy $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitSelect(Segments\Select $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitGroupBy(Segments\GroupBy $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitSelectMany(Segments\SelectMany $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitFilter(Segments\Filter $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitOrderBy(Segments\OrderBy $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitJoin(Segments\Join $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitKeys(Segments\Keys $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitOperation(Segments\Operation $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitRange(Segments\Range $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitUnique(Segments\Unique $segment)
    {
        $this->segments[] = $segment;
    }

    public function visitReindex(Segments\Reindex $segment)
    {
        $this->segments[] = $segment;
    }
}
