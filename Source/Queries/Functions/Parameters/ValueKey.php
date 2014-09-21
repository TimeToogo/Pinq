<?php

namespace Pinq\Queries\Functions\Parameters;

use Pinq\Expressions as O;

/**
 * Parameter structure for the standard element functions.
 * The parameters are passed as the (value, key).
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ValueKey extends ParameterBase
{
    /**
     * @var O\ParameterExpression|null
     */
    protected $value;

    /**
     * @var O\ParameterExpression|null
     */
    protected $key;

    public function __construct(array $parameterExpressions)
    {
        parent::__construct($parameterExpressions, 2);

        $this->value = isset($parameterExpressions[0]) ? $parameterExpressions[0] : null;
        $this->key   = isset($parameterExpressions[1]) ? $parameterExpressions[1] : null;
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

    /**
     * @return boolean
     */
    final public function hasKey()
    {
        return $this->key !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getKey()
    {
        return $this->key;
    }
}
