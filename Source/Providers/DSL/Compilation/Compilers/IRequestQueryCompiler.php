<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IRequestCompilation;
use Pinq\Queries;
use Pinq\Queries\IRequestQuery;
use Pinq\Queries\Requests\IRequestVisitor;

/**
 * Interface of the request query compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestQueryCompiler extends IQueryCompiler, IRequestVisitor
{
    /**
     * @return IRequestQuery
     */
    public function getQuery();

    /**
     * @return IRequestCompilation
     */
    public function getCompilation();
}
