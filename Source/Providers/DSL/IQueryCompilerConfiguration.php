<?php
namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Queries;

/**
 * Interface of the query compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryCompilerConfiguration
{
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
     * Gets the request compiler.
     *
     * @return Compilation\IRequestQueryCompiler
     */
    public function getRequestQueryCompiler();

    /**
     * Builds a request compiler for the supplied request.
     *
     * @return Compilation\IRequestCompiler
     */
    public function getRequestCompiler();

    /**
     * Builds a scope compiler for the supplied scope.
     *
     * @return Compilation\IScopeCompiler
     */
    public function getScopeCompiler();
}
