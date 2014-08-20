<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the static request template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StaticRequestTemplate extends StaticQueryTemplate implements IStaticRequestTemplate
{
    /**
     * @var ICompiledRequest
     */
    protected $compiledQuery;

    public function __construct(Queries\IParameterRegistry $parameters, ICompiledRequest $compiledRequestQuery)
    {
        parent::__construct($parameters, $compiledRequestQuery);
    }
}
