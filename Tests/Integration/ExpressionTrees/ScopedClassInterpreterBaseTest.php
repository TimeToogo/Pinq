<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class ParentStaticClass
{
    const PARENT_TYPE = __CLASS__;

    public static function StaticMethod()
    {
        return __METHOD__;
    }
}

class StaticClass extends ParentStaticClass
{
    const CLASS_TYPE = __CLASS__;

    public static function StaticMethod()
    {
        return __METHOD__;
    }
}

abstract class ScopedClassInterpreterBaseTest extends InterpreterTest
{
    public static function StaticMethod()
    {
        return __METHOD__;
    }

    final protected function assertRecompilesWithRebind(\Closure $closure)
    {
        $this->assertRecompilesCorrectly($closure);

        //In StaticClass scope
        // $boundClosure = \Closure::bind($closure, null, StaticClass::CLASS_TYPE);
        // $this->assertRecompilesCorrectly($boundClosure);

        // //With $this = new StaticClass()
        // $boundClosure = \Closure::bind($closure, new StaticClass());
        // $this->assertRecompilesCorrectly($boundClosure);
    }
}
