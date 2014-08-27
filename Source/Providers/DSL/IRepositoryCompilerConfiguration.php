<?php

namespace Pinq\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Queries;

/**
 * Interface of the repository compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRepositoryCompilerConfiguration extends IQueryCompilerConfiguration
{
    /**
     * @return Configuration\IRepositoryConfiguration
     */
    public function getQueryConfiguration();

    /**
     * Loads the compiled query from the supplied operation expression and assigns
     * the resolved parameters to the $resolvedParameters parameter.
     *
     * @param O\Expression                       $operationExpression
     * @param O\IEvaluationContext|null          $evaluationContext
     * @param Queries\IResolvedParameterRegistry $resolvedParameters
     *
     * @return Compilation\ICompiledOperation
     */
    public function loadCompiledOperationQuery(
            O\Expression $operationExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null
    );
}
