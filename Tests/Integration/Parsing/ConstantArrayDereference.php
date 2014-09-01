<?php

namespace Pinq\Tests\Integration\Parsing;

class ConstantArrayDereference
{
    const TYPE = __CLASS__;

    public static function classConstantArrayDereference()
    {
        ConstantArrayDereference::SOME_ARRAY['foo'];
    }

    public static function constantArrayDereference()
    {
        SOME_ARRAY['foo'];
    }
}