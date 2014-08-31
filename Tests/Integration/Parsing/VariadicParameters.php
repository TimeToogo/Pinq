<?php

namespace Pinq\Tests\Integration\Parsing;

class VariadicParameters
{
    const TYPE = __CLASS__;

    public function simpleVariadic()
    {
        function (...$arguments) {};
    }

    public function onlyArraysByRef()
    {
        function (array &...$arguments) {};
    }

    public function argumentUnpacking()
    {
        func(...[]);
    }
}