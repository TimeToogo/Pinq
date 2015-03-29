<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IOperationCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\IOperationQuery;
use Pinq\Queries\Requests;

/**
 * Implementation of the operation query compiler using the visitor pattern.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OperationQueryCompiler extends QueryCompiler implements IOperationQueryCompiler
{
    /**
     * @var IOperationCompilation
     */
    protected $compilation;

    /**
     * @var IOperationQuery
     */
    protected $query;

    public function __construct(
            IOperationQuery $query,
            IOperationCompilation $compilation,
            IScopeCompiler $scopeCompiler
    ) {
        parent::__construct($query, $compilation, $scopeCompiler);
    }

    /**
     * @return void
     */
    protected function compileQuery()
    {
        $this->query->getOperation()->traverse($this);
    }
}
