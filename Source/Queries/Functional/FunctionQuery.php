<?php

namespace Pinq\Queries\Functional;

abstract class FunctionQuery extends Query
{
    /**
     * @var callable
     */
    private $Function;

    public function __construct(callable $Function)
    {
        $this->Function = $Function;
    }

    final public function GetFunction()
    {
        return $this->Function;
    }
}
