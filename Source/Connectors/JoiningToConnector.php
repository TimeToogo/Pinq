<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;

/**
 * Implements the result API for a join / group join queryable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningToConnector implements 
        Interfaces\IJoiningToTraversable, 
        Interfaces\IJoiningToCollection,
        Interfaces\IJoiningToQueryable,
        Interfaces\IJoiningToRepository
{
    /**
     * @var callable
     */
    private $implementationFactory;

    public function __construct(callable $implementationFactory)
    {
        $this->implementationFactory = $implementationFactory;
    }

    public function to(callable $joinFunction)
    {
        $implementationFactory = $this->implementationFactory;

        return $implementationFactory($joinFunction);
    }
}
