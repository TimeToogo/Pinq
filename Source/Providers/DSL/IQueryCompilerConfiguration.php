<?php
namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Queries;

/**
 * Interface of the query compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryCompilerConfiguration
{
    /**
     * @return Configuration\IQueryConfiguration
     */
    public function getQueryConfiguration();

    /**
     * Gets the cache adapter to store the query templates and
     * compiled queries.
     *
     * @param Queries\ISourceInfo $sourceInfo
     *
     * @return Caching\ICacheAdapter
     */
    public function getCompiledQueryCache(Queries\ISourceInfo $sourceInfo);

    /**
     * Gets a unique string identifier of the compiled request query.
     *
     * @param O\Expression              $requestExpression
     * @param O\IEvaluationContext|null $evaluationContext
     *
     * @return string
     */
    public function getCompiledRequestQueryHash(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null
    );

    /**
     * Loads the compiled query from the supplied request expression and assigns
     * the resolved parameters to the $resolvedParameters parameter.
     *
     * @param O\Expression                       $requestExpression
     * @param O\IEvaluationContext|null          $evaluationContext
     * @param Queries\IResolvedParameterRegistry $resolvedParameters
     *
     * @return Compilation\ICompiledRequest
     */
    public function loadCompiledRequestQuery(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            /* out */ Queries\IResolvedParameterRegistry &$resolvedParameters = null
    );
}
