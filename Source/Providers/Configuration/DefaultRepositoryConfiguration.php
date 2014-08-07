<?php

namespace Pinq\Providers\Configuration;

use Pinq\Queries\Builders;

/**
 * Implementation of the repository configuration using standard
 * classes from the Pinq library.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DefaultRepositoryConfiguration extends DefaultQueryConfiguration implements IRepositoryConfiguration
{
    /**
     * @var Builders\IOperationQueryBuilder
     */
    protected $operationQueryBuilder;

    public function __construct()
    {
        parent::__construct();
        $this->operationQueryBuilder  = $this->buildOperationQueryBuilder();
    }

    protected function buildOperationQueryBuilder()
    {
        return new Builders\OperationQueryBuilder($this->scopeBuilder);
    }

    final public function getOperationQueryBuilder()
    {
        return $this->operationQueryBuilder;
    }
}
