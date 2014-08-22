<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\IQueryable;
use Pinq\Queries\Builders\Functions\IFunction;
use Pinq\Queries;

/**
 * Interface for scope interpretations.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeInterpretation
{
    /**
     * @return IScopeInterpretation
     */
    public function buildScopeInterpretation();

    /**
     * @return ISourceInterpretation
     */
    public function buildSourceInterpretation();

    /**
     * @return IJoinOptionsInterpretation
     */
    public function buildJoinOptionsInterpretation();

    public function interpretScopeSource(IQueryable $queryable);

    public function interpretWhere($segmentId, IFunction $predicate);

    public function interpretOrderings($segmentId, array $orderings);

    public function interpretSlice($segmentId, $startId, $start, $amountId, $amount);

    public function interpretIndexBy($segmentId, IFunction $projection);

    public function interpretKeys($segmentId);

    public function interpretReindex($segmentId);

    public function interpretGroupBy($segmentId, IFunction $projection);

    public function interpretJoin(
            $segmentId,
            IJoinOptionsInterpretation $joinOptionsInterpretation,
            IFunction $joinToFunction
    );

    public function interpretSelect($segmentId, IFunction $projection);

    public function interpretSelectMany($segmentId, IFunction $projection);

    public function interpretUnique($segmentId);

    public function interpretOperation($segmentId, $operationType, ISourceInterpretation $sourceInterpretation);
}
