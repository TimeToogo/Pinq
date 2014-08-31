<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class UsedFunctionsAndConstantsWithExpressionTest extends InterpreterTest
{
    protected function setUp()
    {
        if (!version_compare(PHP_VERSION, '5.6.0-alpha3', '>=')) {
            $this->markTestSkipped('Requires >=PHP 5.6');
        }
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedFunction()
    {
        $this->assertRecompilesCorrectly([UsedFunctionsAndConstantsWithExpression::class, 'usedFunction']);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedConstantWithScalarExpression()
    {
        $this->assertRecompilesCorrectly([UsedFunctionsAndConstantsWithExpression::class, 'usedConstantWithScalarExpression']);
    }
}
