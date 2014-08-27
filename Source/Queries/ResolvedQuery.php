<?php

namespace Pinq\Queries;

use Pinq\IQueryable;

/**
 * Implementation of the resolved query interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResolvedQuery implements IResolvedQuery
{
    /**
     * @var IQueryable
     */
    protected $queryable;

    /**
     * @var array<string, string>
     */
    protected $resolvedParameters;

    /**
     * @var string
     */
    protected $hash;

    public function __construct(IQueryable $queryable, array $resolvedParameters, $hash)
    {
        $this->queryable          = $queryable;
        $this->resolvedParameters = $resolvedParameters;
        $this->hash               = $hash;
    }

    public function getQueryable()
    {
        return $this->queryable;
    }

    public function getResolvedParameters()
    {
        return $this->resolvedParameters;
    }

    public function getHash()
    {
        return $this->hash;
    }
}
