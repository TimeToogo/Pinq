<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Queries;

/**
 * Base class for operation query compilers.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OperationQueryCompiler extends QueryCompiler implements IOperationQueryCompiler
{
    /**
     * @var IOperationCompiler
     */
    protected $operationCompiler;

    public function __construct(IOperationCompiler $operationCompiler, IScopeCompiler $scopeCompiler)
    {
        parent::__construct($scopeCompiler);
        $this->operationCompiler = $operationCompiler;
    }

    protected function compileQuery(
            $compilation,
            Queries\IOperationQuery $operationQuery,
            Queries\IResolvedParameterRegistry $parameters
    ) {
        $this->compileScope($compilation, $operationQuery, $parameters);
        $this->operationCompiler->compileOperation(
                $compilation,
                $this->scopeCompiler,
                $operationQuery->getOperation(),
                $parameters
        );
    }
}
