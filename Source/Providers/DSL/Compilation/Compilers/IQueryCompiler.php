<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IQueryCompilation;
use Pinq\Queries;
use Pinq\Queries\IQuery;

/**
 * Interface of the query compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryCompiler
{
    /**
     * @return IQuery
     */
    public function getQuery();

    /**
     * @return IQueryCompilation
     */
    public function getCompilation();

    /**
     * @return IScopeCompiler
     */
    public function getScopeCompiler();

    /**
     * @return void
     */
    public function compile();
}
