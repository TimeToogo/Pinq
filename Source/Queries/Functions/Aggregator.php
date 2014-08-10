<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Structure of a function that aggregates the values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Aggregator extends Base
{
    protected function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\AggregateValues($parameterExpressions);
    }

    /**
     * @return Parameters\AggregateValues
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}
