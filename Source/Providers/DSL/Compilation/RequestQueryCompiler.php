<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the request query compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RequestQueryCompiler extends QueryCompiler implements IRequestQueryCompiler
{
    /**
     * @var IRequestCompiler
     */
    protected $requestCompiler;

    public function __construct(IRequestCompiler $requestCompiler, IScopeCompiler $scopeCompiler)
    {
        parent::__construct($scopeCompiler);
        $this->requestCompiler = $requestCompiler;
    }

    protected function compileQuery(
            $compilation,
            Queries\IRequestQuery $requestQuery,
            Queries\IResolvedParameterRegistry $parameters
    ) {
        $this->compileScope($compilation, $requestQuery, $parameters);
        $this->requestCompiler->compileRequest(
                $compilation,
                $this->scopeCompiler,
                $requestQuery->getRequest(),
                $parameters
        );
    }
}
