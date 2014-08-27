<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers;
use Pinq\Queries;

/**
 * Base class for query providers that compile query expressions into
 * another DSL.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryProvider extends Providers\QueryProvider
{
    /**
     * @var IQueryCompilerConfiguration
     */
    protected $compilerConfiguration;

    public function __construct(
            Queries\ISourceInfo $sourceInfo,
            IQueryCompilerConfiguration $compilerConfiguration
    ) {
        parent::__construct($sourceInfo, $compilerConfiguration->getQueryConfiguration());

        $this->compilerConfiguration = $compilerConfiguration;
    }

    /**
     * @return IQueryCompilerConfiguration
     */
    public function getCompilerConfiguration()
    {
        return $this->compilerConfiguration;
    }

    public function loadRequestExpression(O\Expression $requestExpression)
    {
        $compiledQuery = $this->compilerConfiguration->loadCompiledRequestQuery(
                $requestExpression,
                null,
                $resolvedParameters
        );

        return $this->loadCompiledRequest($compiledQuery, $resolvedParameters);
    }

    abstract protected function loadCompiledRequest(
            Compilation\ICompiledRequest $compiledRequest,
            Queries\IResolvedParameterRegistry $parameters
    );

    final protected function loadRequest(
            Queries\IRequestQuery $request,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        //Due to overriding parent::loadRequestExpression, this should never be called.
        throw \Pinq\PinqException::notSupported(__METHOD__);
    }
}
