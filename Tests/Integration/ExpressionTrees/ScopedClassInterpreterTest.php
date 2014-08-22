<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class ScopedClassInterpreterTest extends ScopedClassInterpreterBaseTest
{
    public static function StaticMethod()
    {
        return __METHOD__;
    }

    /**
     * @dataProvider interpreters
     */
    public function testSelf()
    {
        $this->assertRecompilesWithRebind(function () { return self::StaticMethod(); });
    }

    /**
     * @dataProvider interpreters
     */
    public function testStatic()
    {
        $this->assertRecompilesWithRebind(function () { return static::StaticMethod(); });
    }

    /**
     * @dataProvider interpreters
     */
    public function testParent()
    {
        $this->assertRecompilesWithRebind(function () { return parent::StaticMethod(); });
    }
}
