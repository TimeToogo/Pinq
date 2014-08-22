<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation;

use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\IRepositoryCompilerConfiguration;
use Pinq\Providers\DSL\RepositoryProvider;
use Pinq\Queries;

class DummyDSLRepositoryProvider extends RepositoryProvider
{
    public function __construct(
            Queries\ISourceInfo $sourceInfo,
            IRepositoryCompilerConfiguration $compilerConfiguration
    ) {
        parent::__construct(
                $sourceInfo,
                $compilerConfiguration,
                new DummyDSLQueryProvider($sourceInfo, $compilerConfiguration)
        );
    }

    protected function executeCompiledOperation(
            Compilation\ICompiledOperation $compiledOperation,
            Queries\IResolvedParameterRegistry $parameters
    ) {
        return null;
    }
}
