<?php

namespace Pinq\Providers\Utilities;

use Pinq\Expressions as O;

/**
 * Interface of the query result collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryResultCollection
{
    /**
     * Saves the query results for the supplied expression.
     *
     * @param O\Expression $queryExpression
     * @param mixed        $results
     *
     * @return mixed
     */
    public function saveResults(O\Expression $queryExpression, $results);

    /**
     * Optimizes the expression for caching purposes.
     *
     * @param O\Expression $queryExpression
     *
     * @return O\Expression
     */
    public function optimizeQuery(O\Expression $queryExpression);

    /**
     * Attempts to find the appropriate query results from the supplied expression.
     * If the exact results cannot be found, the query expression is traversed
     * to find whether an appropriate parent query result is available. If so
     * the results computed from the remaining query scope expression.
     *
     * @param O\Expression $queryExpression
     * @param mixed        &$results        If successful, the results will be set to this reference parameter
     *
     * @return boolean Whether the results were successfully computed
     */
    public function tryComputeResults(O\Expression $queryExpression, &$results);

    /**
     * Computes the results of the request query expression.
     *
     * @param O\Expression $queryExpression
     *
     * @return mixed               The results
     * @throws \Pinq\PinqException
     */
    public function computeResults(O\Expression $queryExpression);

    /**
     * Removes the query result associated with the supplied expression.
     *
     * @param O\Expression $queryExpression
     *
     * @return void
     */
    public function removeResults(O\Expression $queryExpression);

    /**
     * Clears the query results.
     *
     * @return void
     */
    public function clearResults();
}
