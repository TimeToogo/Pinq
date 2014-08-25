<?php
namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;

/**
 * Interface of the request query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestQueryBuilder extends IQueryBuilder
{
    /**
     * Parses the supplied expression into a request query template.
     *
     * @param O\Expression              $expression
     * @param O\IEvaluationContext|null $evaluationContext
     *
     * @return Queries\IRequestQuery
     */
    public function parseRequest(
            O\Expression $expression,
            O\IEvaluationContext $evaluationContext = null
    );

    /**
     * Resolves the query requirements from the supplied expression.
     *
     * @param O\Expression              $expression
     * @param O\IEvaluationContext|null $evaluationContext
     *
     * @return Queries\IResolvedQuery
     */
    public function resolveRequest(
            O\Expression $expression,
            O\IEvaluationContext $evaluationContext = null
    );
}
