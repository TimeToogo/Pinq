<?php

namespace Pinq\Queries;

use Pinq\Expressions as O;

/**
 * Interface for a resolved query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IResolvedQuery
{
    /**
     * Gets the array of resolved values indexed by their respective
     * parameter name.
     *
     * @return array<string, string>
     */
    public function getResolvedParameters();

    /**
     * Gets the unique string identifier for the query.
     *
     * @return string
     */
    public function getHash();
}
