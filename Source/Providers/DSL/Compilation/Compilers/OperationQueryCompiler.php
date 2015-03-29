<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\PinqException;
use Pinq\Providers\DSL\Compilation\IOperationCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\IOperationQuery;
use Pinq\Queries\Requests;
use Pinq\Utilities;

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
            IScopeCompiler $scopeCompiler
    ) {
        parent::__construct($query, $scopeCompiler);

        if(!($scopeCompiler->getCompilation() instanceof IOperationCompilation)) {
            throw new PinqException(
                    'Cannot construct %s: scope compiler query compilation must be an instance of %s, %s given',
                    get_class($this),
                    IOperationCompilation::IOPERATION_COMPILATION_TYPE,
                    Utilities::getTypeOrClass($scopeCompiler->getCompilation())
            );
        }
    }

    /**
     * @return void
     */
    protected function compileQuery()
    {
        $this->query->getOperation()->traverse($this);
    }
}
