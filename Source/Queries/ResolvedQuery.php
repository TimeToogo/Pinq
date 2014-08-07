<?php

namespace Pinq\Queries;

use Pinq\Expressions as O;

/**
 * Implementation of the resolved query interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResolvedQuery implements IResolvedQuery
{
    /**
     * @var array<string, string>
     */
    protected $resolvedParameters;

    /**
     * @var string
     */
    protected $hash;

    public function __construct(array $resolvedParameters, $hash)
    {
        $this->resolvedParameters = $resolvedParameters;
        $this->hash               = $hash;
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
