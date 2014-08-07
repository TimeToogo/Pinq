<?php
namespace Pinq\Providers\DSL;

/**
 * Interface of the repository compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRepositoryCompilerConfiguration extends IQueryCompilerConfiguration
{
    /**
     * Gets the operation compiler.
     *
     * @return Compilation\IOperationCompiler
     */
    public function getOperationCompiler();

    /**
     * Gets the operation query compiler.
     *
     * @return Compilation\IOperationQueryCompiler
     */
    public function getOperationQueryCompiler();
}