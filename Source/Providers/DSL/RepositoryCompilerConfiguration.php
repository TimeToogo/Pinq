<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Iterators;
use Pinq\Parsing;
use Pinq\Queries\Builders;

/**
 * Base class of the repository compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RepositoryCompilerConfiguration extends QueryCompilerConfiguration implements IRepositoryCompilerConfiguration
{
    /**
     * @var Compilation\IOperationCompiler
     */
    protected $operationCompiler;

    /**
     * @var Compilation\IOperationQueryCompiler
     */
    protected $operationQueryCompiler;

    public function __construct()
    {
        parent::__construct();
        $this->operationCompiler = $this->buildOperationCompiler();
        $this->operationQueryCompiler = $this->buildOperationQueryCompiler();
    }

    /**
     * @return Compilation\IOperationCompiler
     */
    abstract protected function buildOperationCompiler();

    /**
     * @return Compilation\IOperationQueryCompiler
     */
    abstract protected function buildOperationQueryCompiler();

    public function getOperationCompiler()
    {
        return $this->operationCompiler;
    }

    public function getOperationQueryCompiler()
    {
        return $this->operationQueryCompiler;
    }
}
