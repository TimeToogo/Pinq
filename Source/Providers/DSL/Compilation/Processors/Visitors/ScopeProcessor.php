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
abstract class ScopeProcessor extends Processors\ScopeProcessor implements Segments\ISegmentVisitor
{
    /**
     * @var Queries\ISegment[]
     */
    private $segments;

    /**
     * @param Queries\ISegment[] $segments
     *
     * @return Queries\ISegment[]
     */
    protected function processSegments(array $segments)
    {
        $this->segments = [];

        foreach ($segments as $segment) {
            $this->segments[] = $segment->traverse($this);
        }

        return $this->segments;
    }

    public function processSource(Queries\Common\ISource $source)
    {
        if ($source instanceof Queries\Common\Source\QueryScope) {
            $processor = $this->forSubScope($source->getScope());

            return $source->update($processor->buildScope());
        }

        return $source;
    }

    public function visitIndexBy(Segments\IndexBy $segment)
    {
        return $segment;
    }

    public function visitSelect(Segments\Select $segment)
    {
        return $segment;
    }

    public function visitGroupBy(Segments\GroupBy $segment)
    {
        return $segment;
    }

    public function visitSelectMany(Segments\SelectMany $segment)
    {
        return $segment;
    }

    public function visitFilter(Segments\Filter $segment)
    {
        return $segment;
    }

    public function visitOrderBy(Segments\OrderBy $segment)
    {
        return $segment;
    }

    public function visitJoin(Segments\Join $segment)
    {
        return $segment->update(
                $segment->getOptions()->updateSource(
                        $this->processSource($segment->getOptions()->getSource())
                ),
                $segment->getJoiningFunction()
        );
    }

    public function visitKeys(Segments\Keys $segment)
    {
        return $segment;
    }

    public function visitOperation(Segments\Operation $segment)
    {
        return $segment->updateSource(
                $this->processSource($segment->getSource())
        );
    }

    public function visitRange(Segments\Range $segment)
    {
        return $segment;
    }

    public function visitUnique(Segments\Unique $segment)
    {
        return $segment;
    }

    public function visitReindex(Segments\Reindex $segment)
    {
        return $segment;
    }
}
