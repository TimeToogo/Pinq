<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Base class of the static query template interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class StaticQueryTemplate extends QueryTemplate implements IStaticQueryTemplate
{
    /**
     * @var ICompiledQuery
     */
    protected $compiledQuery;

    public function __construct(Queries\IParameterRegistry $parameters, ICompiledQuery $compiledQuery)
    {
        parent::__construct(null, $parameters, Parameters\ParameterRegistry::none());

        $this->compiledQuery = $compiledQuery;
    }

    public function getCompiledQuery()
    {
        return $this->compiledQuery;
    }
}
