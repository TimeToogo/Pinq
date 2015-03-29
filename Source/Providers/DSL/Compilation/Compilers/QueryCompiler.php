<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IQueryCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\IQuery;
use Pinq\Queries\Requests;

/**
 * Base class of the query compiler
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryCompiler extends Compiler implements IQueryCompiler
{
    /**
     * @var IQuery
     */
    protected $query;

    /**
     * @var IScopeCompiler
     */
    protected $scopeCompiler;

    public function __construct(IQuery $query, IQueryCompilation $compilation, IScopeCompiler $scopeCompiler)
    {
        parent::__construct($compilation);
        $this->query         = $query;
        $this->scopeCompiler = $scopeCompiler;
    }

    /**
     * @return IQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return IScopeCompiler
     */
    public function getScopeCompiler()
    {
        return $this->scopeCompiler;
    }

    /**
     * @return void
     */
    final public function compile()
    {
        $this->scopeCompiler->compile();
        $this->compileQuery();
    }

    /**
     * @return void
     */
    protected abstract function compileQuery();
}
