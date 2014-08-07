<?php

namespace Pinq\Queries\Builders\Functions;

use Pinq\Expressions as O;

/**
 * Query parameter of a function represented by a closure expression tree.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClosureExpressionFunction extends BaseFunction
{
    /**
     * @var O\ClosureExpression
     */
    private $expression;

    /**
     * @var callable|null
     */
    private $callable;

    /**
     * @var string|null
     */
    private $scopeType;

    public function __construct($id, O\ClosureExpression $expression, $scopeType = null)
    {
        parent::__construct($id);
        $this->expression = $expression;
        $this->scopeType  = $scopeType;
    }

    public function getType()
    {
        return self::CLOSURE_EXPRESSION;
    }

    /**
     * @return O\ClosureExpression
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return boolean
     */
    public function hasScopeType()
    {
        return $this->scopeType !== null;
    }

    /**
     * @return string|null
     */
    public function getScopeType()
    {
        return $this->scopeType;
    }

    public function getCallable()
    {
        if ($this->callable === null) {
            $this->callable = eval('return ' . $this->expression->compile() . ';');
            if ($this->scopeType !== null) {
                $this->callable = \Closure::bind($this->callable, null, $this->scopeType);
            }
        }

        return $this->callable;
    }
}
