<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\IQueryable;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries\Functions;
use Pinq\Queries;

/**
 * Implementation of the scope resolver.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeResolver extends BaseResolver implements IScopeResolver
{
    /**
     * @var IQueryable
     */
    protected $queryable;

    /**
     * @return IQueryable
     */
    public function getQueryable()
    {
        return $this->queryable;
    }

    public function buildScopeInterpretation()
    {
        return new self($this->functionInterpreter);
    }

    public function buildSourceInterpretation()
    {
        return new SourceResolver($this->functionInterpreter);
    }

    public function buildJoinOptionsInterpretation()
    {
        return new JoinOptionsResolver($this->functionInterpreter);
    }

    public function interpretScopeSource(IQueryable $queryable)
    {
        $this->queryable = $queryable;
        $this->appendToHash($queryable->getSourceInfo()->getHash());
    }

    public function interpretWhere($segmentId, IFunction $predicate)
    {
        $this->appendToHash($segmentId);
        $this->resolveFunction($predicate);
    }

    public function interpretOrderings($segmentId, array $orderings)
    {
        $this->appendToHash($segmentId);

        foreach ($orderings as $ordering) {
            list($projection, $isAscendingId, $isAscendingValue) = $ordering;
            $this->resolveFunction($projection);
            $this->resolveParameter($isAscendingId, $isAscendingValue);
        }
    }

    public function interpretSlice($segmentId, $startId, $start, $amountId, $amount)
    {
        $this->appendToHash($segmentId);
        $this->resolveParameter($startId, $start);
        $this->resolveParameter($amountId, $amount);
    }

    public function interpretIndexBy($segmentId, IFunction $projection)
    {
        $this->appendToHash($segmentId);
        $this->resolveFunction($projection);
    }

    public function interpretKeys($segmentId)
    {
        $this->appendToHash($segmentId);
    }

    public function interpretReindex($segmentId)
    {
        $this->appendToHash($segmentId);
    }

    public function interpretGroupBy($segmentId, IFunction $projection)
    {
        $this->appendToHash($segmentId);
        $this->resolveFunction($projection);
    }

    public function interpretJoin(
            $segmentId,
            IJoinOptionsInterpretation $joinOptionsInterpretation,
            IFunction $joinToFunction
    ) {
        /* @var $joinOptionsInterpretation IJoinOptionsResolver */
        $this->appendToHash($segmentId);
        $this->resolveParametersFrom($joinOptionsInterpretation);
        $this->resolveFunction($joinToFunction);
    }

    public function interpretSelect($segmentId, IFunction $projection)
    {
        $this->appendToHash($segmentId);
        $this->resolveFunction($projection);
    }

    public function interpretSelectMany($segmentId, IFunction $projection)
    {
        $this->appendToHash($segmentId);
        $this->resolveFunction($projection);
    }

    public function interpretUnique($segmentId)
    {
        $this->appendToHash('unique');
    }

    public function interpretOperation($segmentId, $operationType, ISourceInterpretation $sourceInterpretation)
    {
        /* @var $sourceInterpretation ISourceResolver */
        $this->appendToHash($segmentId);
        $this->resolveParametersFrom($sourceInterpretation);
    }
}
