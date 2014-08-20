<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the static operation template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StaticOperationTemplate extends StaticQueryTemplate implements IStaticOperationTemplate
{
    /**
     * @var ICompiledOperation
     */
    protected $compiledQuery;

    public function __construct(Queries\IParameterRegistry $parameters, ICompiledOperation $compiledOperationQuery)
    {
        parent::__construct($parameters, $compiledOperationQuery);
    }
}
