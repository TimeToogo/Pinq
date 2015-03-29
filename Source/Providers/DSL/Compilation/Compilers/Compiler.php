<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IQueryCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\Segments;

/**
 * Base class of the compiler
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Compiler
{
    /**
     * @var IQueryCompilation
     */
    protected $compilation;

    public function __construct(IQueryCompilation $compilation)
    {
        $this->compilation = $compilation;
    }

    /**
     * @return IQueryCompilation
     */
    public function getCompilation()
    {
        return $this->compilation;
    }
}
