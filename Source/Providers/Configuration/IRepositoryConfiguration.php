<?php

namespace Pinq\Providers\Configuration;

use Pinq\Queries;
use Pinq\Queries\Builders;

/**
 * Interface for the configurable services of the repository
 * provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRepositoryConfiguration extends IQueryConfiguration
{
    /**
     * @return Builders\IOperationQueryBuilder
     */
    public function getOperationQueryBuilder();
}
