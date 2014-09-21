<?php

namespace Pinq\Connectors;

use Pinq\Expressions as O;
use Pinq\Interfaces;
use Pinq\Providers;
use Pinq\Queries;
use Pinq\Queries\Common\Join;
use Pinq\QueryBuilder;

/**
 * Implements the filtering API for a join / group join queryable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoiningQueryable extends QueryBuilder implements Interfaces\IJoiningOnQueryable
{
    public function __construct(Providers\IQueryProvider $provider, O\TraversalExpression $queryExpression)
    {
        parent::__construct($provider, $queryExpression);
    }

    public function on(callable $joiningOnFunction)
    {
        $this->expression = $this->newMethod(__FUNCTION__, [$joiningOnFunction]);

        return $this;
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        $this->expression = $this->newMethod(__FUNCTION__, [$outerKeyFunction, $innerKeyFunction]);

        return $this;
    }

    public function withDefault($value, $key = null)
    {
        $this->expression = $this->newMethod(__FUNCTION__, [$value, $key]);

        return $this;
    }

    public function to(callable $joinFunction)
    {
        return $this->newMethodSegment(__FUNCTION__, [$joinFunction]);
    }
}
