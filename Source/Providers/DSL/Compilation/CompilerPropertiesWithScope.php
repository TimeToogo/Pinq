<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Trait containing the common compilation properties for compiling parts of the query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait CompilerPropertiesWithScope
{
    use CompilerProperties;

    /**
     * The scope compiler
     *
     * @var IScopeCompiler
     */
    protected $scopeCompiler;

    protected function runCompileWithScope(
            $compilation,
            IScopeCompiler $scopeCompiler,
            Queries\IResolvedParameterRegistry $structuralParameters,
            callable $compileCallback
    ) {
        $this->runCompile(
                $compilation,
                $structuralParameters,
                function () use ($scopeCompiler, $compileCallback) {
                    $oldScopeCompiler = $this->scopeCompiler;
                    $this->scopeCompiler = $scopeCompiler;
                    $compileCallback();
                    $this->scopeCompiler = $oldScopeCompiler;
                }
        );
    }
}
