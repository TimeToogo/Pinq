<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching\ICacheAdapter;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers;
use Pinq\Providers\DSL\Compilation\ICompiledOperation;
use Pinq\Providers\DSL\Compilation\IOperationTemplate;
use Pinq\Providers\DSL\Compilation\IStaticOperationTemplate;
use Pinq\Queries;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RepositoryProvider extends Providers\RepositoryProvider
{
    /**
     * @var IRepositoryCompilerConfiguration
     */
    protected $compilerConfiguration;

    /**
     * @var QueryProvider
     */
    protected $queryProvider;

    /**
     * @var Compilation\IOperationQueryCompiler
     */
    protected $operationCompiler;

    /**
     * @var ICacheAdapter
     */
    protected $compiledQueryCache;

    public function __construct(
            Queries\ISourceInfo $sourceInfo,
            IRepositoryCompilerConfiguration $compilerConfiguration,
            QueryProvider $queryProvider,
            Configuration\IRepositoryConfiguration $configuration = null
    ) {
        parent::__construct($sourceInfo, $queryProvider, $configuration);

        $this->compilerConfiguration = $compilerConfiguration;
        $this->operationCompiler     = $compilerConfiguration->getOperationQueryCompiler();
        $this->compiledQueryCache    = $compilerConfiguration->getCompiledQueryCache($sourceInfo);
    }

    /**
     * @return IRepositoryCompilerConfiguration
     */
    public function getCompilerConfiguration()
    {
        return $this->compilerConfiguration;
    }

    public function execute(O\Expression $operationExpression)
    {
        $resolution    = $this->operationQueryBuilder->resolveOperation($operationExpression);
        $queryHash     = $resolution->getHash();
        $queryTemplate = $this->compiledQueryCache->tryGet($queryHash);

        if (!($queryTemplate instanceof Compilation\IOperationTemplate)) {
            $operationQuery = $this->operationQueryBuilder->parseOperation($operationExpression);
            $resolvedParameters = $operationQuery->getParameters()->resolve($resolution);
            $queryTemplate  = $this->operationCompiler->createOperationTemplate($operationQuery, $resolvedParameters);
            $this->compiledQueryCache->save($queryHash, $queryTemplate);
        } else {
            $resolvedParameters = $queryTemplate->getParameters()->resolve($resolution);
        }

        $compiledQuery      = $this->getCompiledQuery($queryHash, $queryTemplate, $resolvedParameters);

        return $this->executeCompiledOperation($compiledQuery, $resolvedParameters);
    }

    final protected function getCompiledQuery(
            $queryHash,
            IOperationTemplate $template,
            Queries\IResolvedParameterRegistry $parameters
    ) {
        if ($template instanceof IStaticOperationTemplate) {
            return $template->getCompiledQuery();
        } else {
            $compiledQueryHash = $queryHash . '-' . $template->getCompiledQueryHash($parameters);
            $compiledQuery     = $this->compiledQueryCache->tryGet($compiledQueryHash);

            if (!($compiledQuery instanceof ICompiledOperation)) {
                $compiledQuery = $this->operationCompiler->compileOperationQuery($template, $parameters);
                $this->compiledQueryCache->save($compiledQueryHash, $compiledQuery);
            }

            return $compiledQuery;
        }
    }

    abstract protected function executeCompiledOperation(
            Compilation\ICompiledOperation $compiledOperation,
            Queries\IResolvedParameterRegistry $parameters
    );

    protected function executeOperation(
            Queries\IOperationQuery $operation,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        //Overrides parent::execute
        throw \Pinq\PinqException::notSupported(__METHOD__);
    }
}
