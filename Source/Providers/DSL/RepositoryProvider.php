<?php

namespace Pinq\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\PinqException;
use Pinq\Providers\Configuration;
use Pinq\Providers;
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

    public function __construct(
            Queries\ISourceInfo $sourceInfo,
            IRepositoryCompilerConfiguration $compilerConfiguration,
            QueryProvider $queryProvider,
            Configuration\IRepositoryConfiguration $configuration = null
    ) {
        parent::__construct($sourceInfo, $queryProvider, $configuration);

        $this->compilerConfiguration = $compilerConfiguration;;
    }

    /**
     * @return IRepositoryCompilerConfiguration
     */
    public function getCompilerConfiguration()
    {
        return $this->compilerConfiguration;
    }

    public function executeOperationExpression(O\Expression $operationExpression)
    {
        $compiledQuery = $this->compilerConfiguration->loadCompiledOperationQuery(
                $operationExpression,
                null,
                /* out */ $resolvedParameters
        );

        $this->executeCompiledOperation($compiledQuery, $resolvedParameters);
    }

    abstract protected function executeCompiledOperation(
            Compilation\ICompiledOperation $compiledOperation,
            Queries\IResolvedParameterRegistry $parameters
    );

    protected function executeOperation(
            Queries\IOperationQuery $operation,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        //Overrides parent::executeOperationExpression
        throw PinqException::notSupported(__METHOD__);
    }
}
