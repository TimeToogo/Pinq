<?php

namespace Pinq\Queries\Functions\Parameters;

use Pinq\Expressions as O;

/**
 * Parameter structure for the connector functions.
 * The parameters are passed as the (outerValue, innerValue, outerKey, innerKey).
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OuterInnerValueKey extends ParameterBase
{
    /**
     * @var O\ParameterExpression|null
     */
    protected $outerValue;

    /**
     * @var O\ParameterExpression|null
     */
    protected $outerKey;

    /**
     * @var O\ParameterExpression|null
     */
    protected $innerValue;

    /**
     * @var O\ParameterExpression|null
     */
    protected $innerKey;

    public function __construct(array $parameterExpressions)
    {
        parent::__construct($parameterExpressions, 4);

        //(outerValue, innerValue, outerKey, innerKey)
        $this->outerValue = isset($parameterExpressions[0]) ? $parameterExpressions[0] : null;
        $this->innerValue = isset($parameterExpressions[1]) ? $parameterExpressions[1] : null;
        $this->outerKey   = isset($parameterExpressions[2]) ? $parameterExpressions[2] : null;
        $this->innerKey   = isset($parameterExpressions[3]) ? $parameterExpressions[3] : null;
    }

    /**
     * @return boolean
     */
    final public function hasOuterValue()
    {
        return $this->outerValue !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getOuterValue()
    {
        return $this->outerValue;
    }

    /**
     * @return boolean
     */
    final public function hasOuterKey()
    {
        return $this->outerKey !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getOuterKey()
    {
        return $this->outerKey;
    }

    /**
     * @return boolean
     */
    final public function hasInnerValue()
    {
        return $this->innerValue !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getInnerValue()
    {
        return $this->innerValue;
    }

    /**
     * @return boolean
     */
    final public function hasInnerKey()
    {
        return $this->innerKey !== null;
    }

    /**
     * @return O\ParameterExpression|null
     */
    final public function getInnerKey()
    {
        return $this->innerKey;
    }
}
