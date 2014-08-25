<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\IQueryable;
use Pinq\Queries;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Functions;
use Pinq\Queries\Segments;

/**
 * Implementation of the scope parser
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeParser extends BaseParser implements IScopeParser
{
    /**
     * The parsed query segments.
     *
     * @var Queries\ISegment[]
     */
    protected $segments = [];

    /**
     * @var Queries\ISourceInfo
     */
    protected $sourceInfo;

    public function getScope()
    {
        return new Queries\Scope($this->sourceInfo, $this->segments);
    }

    public function buildSourceInterpretation()
    {
        return new SourceParser($this->functionInterpreter);
    }

    public function buildJoinOptionsInterpretation()
    {
        return new JoinOptionsParser($this->functionInterpreter);
    }

    public function buildScopeInterpretation()
    {
        return new self($this->functionInterpreter);
    }

    public function interpretScopeSource(IQueryable $queryable)
    {
        $this->sourceInfo = $queryable->getSourceInfo();
    }

    /**
     * @param IFunction $function
     *
     * @return Functions\ElementProjection
     */
    final protected function buildElementProjection(IFunction $function)
    {
        return $this->buildFunction(
                $function,
                Functions\ElementProjection::factory()
        );
    }

    public function interpretWhere($segmentId, IFunction $predicate)
    {
        $this->segments[] = new Segments\Filter($this->buildElementProjection($predicate));
    }

    public function interpretOrderings($segmentId, array $orderings)
    {
        $orderingSections = [];

        foreach ($orderings as $ordering) {
            list($projection, $isAscendingId, $isAscendingValue) = $ordering;
            $orderingSections[] = new Segments\Ordering(
                    $this->buildElementProjection($projection),
                    $isAscendingId);
        }

        $this->segments[] = new Segments\OrderBy($orderingSections);
    }

    public function interpretSlice($segmentId, $startId, $start, $amountId, $amount)
    {
        $this->segments[] = new Segments\Range($startId, $amountId);
    }

    public function interpretIndexBy($segmentId, IFunction $projection)
    {
        $this->segments[] = new Segments\IndexBy($this->buildElementProjection($projection));
    }

    public function interpretKeys($segmentId)
    {
        $this->segments[] = new Segments\Keys();
    }

    public function interpretReindex($segmentId)
    {
        $this->segments[] = new Segments\Reindex();
    }

    public function interpretGroupBy($segmentId, IFunction $projection)
    {
        $this->segments[] = new Segments\GroupBy($this->buildElementProjection($projection));
    }

    public function interpretJoin(
            $segmentId,
            IJoinOptionsInterpretation $joinOptionsInterpretation,
            IFunction $joinToFunction
    ) {
        /* @var $joinOptionsInterpretation IJoinOptionsParser */
        $this->segments[] = new Segments\Join(
                $joinOptionsInterpretation->getJoinOptions(),
                $this->buildFunction($joinToFunction, Functions\ConnectorProjection::factory()));
    }

    public function interpretSelect($segmentId, IFunction $projection)
    {
        $this->segments[] = new Segments\Select($this->buildElementProjection($projection));
    }

    public function interpretSelectMany($segmentId, IFunction $projection)
    {
        $this->segments[] = new Segments\SelectMany($this->buildElementProjection($projection));
    }

    public function interpretUnique($segmentId)
    {
        $this->segments[] = new Segments\Unique();
    }

    public function interpretOperation($segmentId, $operationType, ISourceInterpretation $sourceInterpretation)
    {
        /* @var $sourceInterpretation ISourceParser */
        $this->segments[] = new Segments\Operation(
                $operationType,
                $sourceInterpretation->getSource());
    }
}
