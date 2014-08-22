<?php

namespace Pinq\Connectors;

use Pinq\Expressions as O;
use Pinq\Interfaces;
use Pinq\Providers;

/**
 * Implements the filtering API for a join / group join repository.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoiningRepository extends JoiningQueryable implements Interfaces\IJoiningOnRepository
{
    /**
     * @var Providers\IRepositoryProvider
     */
    protected $provider;

    public function __construct(Providers\IRepositoryProvider $provider, O\TraversalExpression $queryExpression)
    {
        parent::__construct($provider, $queryExpression);
    }

    public function apply(callable $applyFunction)
    {
        $this->provider->execute($this->newMethod(__FUNCTION__, [$applyFunction]));
    }
}
