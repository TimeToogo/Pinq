<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class FunctionBase
{
    /**
     * @var callable
     */
    protected $function;
    
    /**
     * @var O\ClosureExpression
     */
    protected $functionExpression;

    public function __construct(callable $function, O\ClosureExpression $functionExpression)
    {
        $this->function = $function;
        $this->functionExpression = $functionExpression;
    }
    
    /**
     * @return callable
     */
    final public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return O\ClosureExpression
     */
    final public function getFunctionExpression()
    {
        return $this->functionExpression;
    }

    /**
     * @return O\ParameterExpression[]
     */
    final public function getParameterExpressions()
    {
        return $this->functionExpression->getParameterExpressions();
    }

    public function serialize()
    {
        return serialize($this->functionExpression);
    }
    
    public static function unserialize(callable $function, $serialized)
    {
        return new static($function, unserialize($serialized));
    }
}
