<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class VariadicParameters
{
    public function simpleVariadic(...$arguments)
    {
        return $arguments;
    }

    public function onlyArraysByRef(array &...$arguments)
    {
        return $arguments;
    }

    public function argumentUnpacking(callable $function, array $arguments)
    {
        return $function(...$arguments);
    }
}