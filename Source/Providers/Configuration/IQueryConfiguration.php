<?php

namespace Pinq\Providers\Configuration;

use Pinq\Caching;
use Pinq\Queries\Builders;
use Pinq\Queries;
use Pinq\Iterators\IIteratorScheme;

/**
 * Interface for the configurable services of the query
 * provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryConfiguration
{
    /**
     * @return Caching\IQueryCache
     */
    public function getQueryCache();

    /**
     * @return Queries\Builders\IRequestQueryBuilder
     */
    public function getRequestQueryBuilder();

    /**
     * @return IIteratorScheme
     */
    public function getIteratorScheme();
}
