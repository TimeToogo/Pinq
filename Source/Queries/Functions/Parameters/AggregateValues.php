<?php

namespace Pinq\Queries\Functions\Parameters;

use Pinq\Expressions as O;

/**
 * Parameter structure for the aggregate function.
 * The parameters are passed as the (aggregated value, current value).
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AggregateValues extends ParameterBase
{
    /**
     * @var O\ParameterExpression|null
     */
    protected $aggregateValue;

    /**
     * @var O\ParameterExpression|null
     */
    protected $value;

    public function __construct(array $parameterExpressions)
    {
        parent::__construct($parameterExpressions, 2);

        $this->aggregateValue = isset($parameterExpressions[0]) ? $parameterExpressions[0] : null;
        $this->value          = isset($parameterExpressions[1]) ? $parameterExpressions[1] : null;
    }

    /**
     * @return boolean
     */
    final public function hasAggregateValue()
    {
        return $this->aggregateValue !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getAggregateValue()
    {
        return $this->aggregateValue;
    }

    /**
     * @return boolean
     */
    final public function hasValue()
    {
        return $this->value !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getValue()
    {
        return $this->value;
    }
}
