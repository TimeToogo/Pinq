<?php
namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;

/**
 * Interface of the operation query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationQueryBuilder extends IQueryBuilder
{
    /**
     * Parses the supplied expression into a operation query template.
     *
     * @param O\Expression $expression
     * @param string|null  $closureScopeType
     *
     * @return Queries\IOperationQuery
     */
    public function parseOperation(O\Expression $expression, $closureScopeType = null);

    /**
     * Resolves the query requirements from the supplied expression.
     *
     * @param O\Expression $expression
     *
     * @return Queries\IResolvedQuery
     */
    public function resolveOperation(O\Expression $expression);
}