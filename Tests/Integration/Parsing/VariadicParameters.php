<?php

namespace Pinq\Tests\Integration\Parsing;

class VariadicParameters
{
    public function simpleVaridic()
    {
        return function (...$arguments) {};
    }

    public function onlyArraysByRef()
    {
        return function (array &...$arguments) {};
    }

    public function argumentUnpacking()
    {
        return func(...$arguments);
    }
}