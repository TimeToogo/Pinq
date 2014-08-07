<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;
use Pinq\Queries\Requests;

/**
 * Trait containing the common compilation properties for compiling parts of the query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait CompilerProperties
{
    /**
     * The compilation object.
     *
     * @var mixed
     */
    protected $compilation;

    /**
     * The structural parameters.
     *
     * @var Queries\IResolvedParameterRegistry
     */
    protected $parameters;

    protected function runCompile($compilation, Queries\IResolvedParameterRegistry $structuralParameters, callable $compileCallback)
    {
        $oldCompilation = $this->compilation;
        $oldstructuralParameters= $this->parameters;
        $this->compilation = $compilation;
        $this->parameters = $structuralParameters;
        $compileCallback();
        $this->compilation = $oldCompilation;
        $this->parameters = $oldstructuralParameters;
    }
}