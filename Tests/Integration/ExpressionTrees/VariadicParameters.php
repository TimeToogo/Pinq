<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class VariadicParameters
{
    public function simpleVaridic(...$arguments)
    {
        return $arguments;
    }

    public function onlyArraysByRef(array &...$arguments)
    {
        return $arguments;
    }

    public function argumentUnpacking()
    {
        $function(...[]);
    }
}