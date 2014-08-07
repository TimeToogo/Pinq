<?php

namespace Pinq\Queries\Builders\Functions;

use Pinq\Queries;

/**
 * Query parameter of a function represented by a callable value.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CallableFunction extends BaseFunction
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct($id, callable $callable)
    {
        parent::__construct($id);
        $this->callable = $callable;
    }

    public function getType()
    {
        return self::CALLABLE_VALUE;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }
}
