<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\PinqException;
use Pinq\Providers\DSL\Compilation\IRequestCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\IRequestQuery;
use Pinq\Queries\Requests;
use Pinq\Utilities;

/**
 * Implementation of the request query compiler using the visitor pattern.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RequestQueryCompiler extends QueryCompiler implements IRequestQueryCompiler
{
    /**
     * @var IRequestCompilation
     */
    protected $compilation;

    /**
     * @var IRequestQuery
     */
    protected $query;

    public function __construct(
            IRequestQuery $query,
            IScopeCompiler $scopeCompiler
    ) {
        parent::__construct($query, $scopeCompiler);

        if(!($scopeCompiler->getCompilation() instanceof IRequestCompilation)) {
            throw new PinqException(
                    'Cannot construct %s: scope compiler compilation must be an instance of %s, %s given',
                    get_class($this),
                    IRequestCompilation::IREQUEST_COMPILATION_TYPE,
                    Utilities::getTypeOrClass($scopeCompiler->getCompilation())
            );
        }
    }

    /**
     * @return void
     */
    protected function compileQuery()
    {
        $this->query->getRequest()->traverse($this);
    }
}
