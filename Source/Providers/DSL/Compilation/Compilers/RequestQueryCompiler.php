<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IRequestCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\IRequestQuery;
use Pinq\Queries\Requests;

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
            IRequestCompilation $compilation,
            IScopeCompiler $scopeCompiler
    ) {
        parent::__construct($query, $compilation, $scopeCompiler);
    }

    /**
     * @return void
     */
    protected function compileQuery()
    {
        $this->query->getRequest()->traverse($this);
    }
}
