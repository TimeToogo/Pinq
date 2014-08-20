<?php

namespace Pinq\Queries\Builders\Interpretations;

/**
 * Interface for query expression resolvers.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryResolver
{
    /**
     * @return array<string, mixed>
     */
    public function getResolvedParameters();

    /**
     * @return string
     */
    public function getHash();
}
