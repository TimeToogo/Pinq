<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IOperationCompilation;
use Pinq\Queries;
use Pinq\Queries\IOperationQuery;
use Pinq\Queries\Operations\IOperationVisitor;

/**
 * Interface of the operation query compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationQueryCompiler extends IQueryCompiler, IOperationVisitor
{
    /**
     * @return IOperationQuery
     */
    public function getQuery();

    /**
     * @return IOperationCompilation
     */
    public function getCompilation();
}
