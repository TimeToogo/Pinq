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
     * Loads the compiled query from the supplied request expression and assigns
     * the resolved parameters to the $resolvedParameters parameter.
     *
     * @param Queries\ISourceInfo                $sourceInfo
     * @param O\Expression                       $requestExpression
     * @param O\IEvaluationContext|null          $evaluationContext
     * @param Queries\IResolvedParameterRegistry $resolvedParameters
     *
     * @return Compilation\ICompiledRequest
     */
    public function loadCompiledRequestQuery(
            Queries\ISourceInfo $sourceInfo,
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null
    );
}
