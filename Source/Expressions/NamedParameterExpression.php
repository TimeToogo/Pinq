<?php

namespace Pinq\Expressions;

/**
 * Base class for parameters representing named variables.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class NamedParameterExpression extends Expression
{
    /**
     * @var string
     */
    protected $name;

    protected function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return VariableExpression
     */
    public function asVariable()
    {
        return Expression::variable(Expression::value($this->name));
    }
}
