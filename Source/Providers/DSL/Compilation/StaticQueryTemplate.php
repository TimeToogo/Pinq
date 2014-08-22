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
        parent::__construct($parameters, ParameterCollection::none());

        $this->compiledQuery = $compiledQuery;
    }

    public function getCompiledQuery()
    {
        return $this->compiledQuery;
    }

    final public function getCompiledQueryHash(Queries\IResolvedParameterRegistry $parameters)
    {
        //For the static template, there are no structural parameters and hence an empty hash
        return '';
    }
}
