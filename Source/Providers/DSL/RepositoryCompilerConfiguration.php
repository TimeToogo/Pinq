<?php

namespace Pinq\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
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
        return $this->loadCompiledQuery(
                $operationExpression,
                $evaluationContext,
                $resolvedParameters,
                [$this->operationQueryBuilder, 'resolveOperation'],
                [$this->operationQueryBuilder, 'parseOperation'],
                [$this, 'createOperationTemplate'],
                [$this, 'compileOperationQuery']
        );
    }

    protected function createOperationTemplate(Queries\IOperationQuery $operationQuery)
    {
        $structuralExpressions = $this->locateStructuralExpressions($operationQuery, $structuralExpressionProcessors);

        if ($structuralExpressions->countExpressions() === 0) {
            return new StaticOperationTemplate($operationQuery->getParameters(), $this->buildCompiledOperationQuery(
                    $operationQuery
            ));
        }

        return new OperationTemplate($operationQuery, $structuralExpressions);
    }

    protected function compileOperationQuery(
            Compilation\IOperationTemplate $template,
            Parameters\ResolvedStructuralExpressionRegistry $structuralExpressions
    ) {
        $structuredQuery = $this->inlineStructuralExpressions($template, $structuralExpressions);

        return $this->buildCompiledOperationQuery($structuredQuery);
    }

    /**
     * @param Queries\IOperationQuery $query
     *
     * @return Compilation\ICompiledOperation
     */
    abstract protected function buildCompiledOperationQuery(Queries\IOperationQuery $query);
}
