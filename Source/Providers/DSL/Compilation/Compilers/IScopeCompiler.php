<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IQueryCompilation;
use Pinq\Queries;
use Pinq\Queries\Segments\ISegmentVisitor;

/**
 * Interface of the scope compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeCompiler extends ISegmentVisitor
{
    /**
     * @return IQueryCompilation
     */
    public function getCompilation();

    /**
     * @return Queries\IScope
     */
    public function getScope();

    /**
     * @return void
     */
    public function compile();
}
