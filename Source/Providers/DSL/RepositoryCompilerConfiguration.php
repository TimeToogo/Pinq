<?php

namespace Pinq\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Caching;
use Pinq\Providers\Configuration;
use Pinq\Providers\DSL\Compilation\Compilers\IOperationQueryCompiler;
use Pinq\Providers\DSL\Compilation\OperationTemplate;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\Compilation\StaticOperationTemplate;
use Pinq\Queries;

/**
 * Base class of the repository compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RepositoryCompilerConfiguration extends QueryCompilerConfiguration implements IRepositoryCompilerConfiguration
{
    /**
     * @var Configuration\IRepositoryConfiguration
     */
    protected $queryConfiguration;

    /**
     * @var Queries\Builders\IOperationQueryBuilder
     */
    protected $operationQueryBuilder;

    public function __construct()
    {
        parent::__construct();
        $this->operationQueryBuilder = $this->queryConfiguration->getOperationQueryBuilder();
    }

    protected function buildQueryConfiguration()
    {
        return new Configuration\DefaultRepositoryConfiguration();
    }

    public function loadCompiledOperationQuery(
            O\Expression $operationExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null
    ) {
        $operationTemplate = $this->loadOperationQueryTemplate(
                $operationExpression,
                $evaluationContext,
                $resolvedParameters,
                /* out */ $queryCache,
                /* out */ $templateHash
        );

        return $this->loadCompiledQueryFromTemplate(
                $queryCache,
                $templateHash,
                $operationTemplate,
                $resolvedParameters,
                [$this, 'compileOperationQuery']
        );
    }

    protected function loadOperationQueryTemplate(
            O\Expression $queryExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null,
            /* out */ Caching\ICacheAdapter &$queryCache = null,
            /* out */ &$templateHash)
    {
        return $this->loadQueryTemplate(
                $queryExpression,
                $evaluationContext,
                $resolvedParameters,
                [$this->operationQueryBuilder, 'resolveOperation'],
                [$this->operationQueryBuilder, 'parseOperation'],
                [$this, 'createOperationTemplate'],
                $queryCache,
                $templateHash);
    }

    protected function createOperationTemplate(Queries\IOperationQuery $operationQuery)
    {
        $structuralParameters = $this->locateStructuralParameters($operationQuery);

        if ($structuralParameters->count() === 0) {
            return new StaticOperationTemplate($operationQuery->getParameters(), $this->buildCompiledOperationQuery(
                    $operationQuery
            ));
        }

        return new OperationTemplate($operationQuery, $structuralParameters);
    }

    protected function compileOperationQuery(
            Compilation\IOperationTemplate $template,
            Parameters\ResolvedParameterRegistry $structuralParameters
    ) {
        $structuredQuery = $this->inlineStructuralParameters($template->getQuery(), $structuralParameters);

        return $this->buildCompiledOperationQuery($structuredQuery);
    }

    /**
     * @param Queries\IOperationQuery $query
     *
     * @return Compilation\ICompiledOperation
     */
    protected function buildCompiledOperationQuery(Queries\IOperationQuery $query)
    {
        $compiler = $this->getOperationQueryCompiler($query);
        $compiler->compile();

        return $compiler->getCompilation()->asCompiled();
    }

    /**
     * @param Queries\IOperationQuery $query
     *
     * @return IOperationQueryCompiler
     */
    abstract protected function getOperationQueryCompiler(Queries\IOperationQuery $query);
}
