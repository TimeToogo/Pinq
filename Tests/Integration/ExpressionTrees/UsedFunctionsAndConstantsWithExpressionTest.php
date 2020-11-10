<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class UsedFunctionsAndConstantsWithExpressionTest extends InterpreterTest
{
    protected function setUp(): void
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
        $this->assertRecompilesCorrectly([UsedFunctionsAndConstantsWithExpression::TYPE, 'usedFunction']);
    }

    /**
     * @dataProvider interpreters
     */
    public function testUsedConstantWithScalarExpression()
    {
        $this->assertRecompilesCorrectly(
                [UsedFunctionsAndConstantsWithExpression::TYPE, 'usedConstantWithScalarExpression']
        );
    }

    /**
     * @dataProvider interpreters
     */
    public function testClassConstantArrayDereference()
    {
        $this->assertRecompilesCorrectly(
                [UsedFunctionsAndConstantsWithExpression::TYPE, 'classConstantArrayDereference']
        );
    }

    /**
     * @dataProvider interpreters
     */
    public function testConstantArrayDereference()
    {
        $this->assertRecompilesCorrectly(
                [UsedFunctionsAndConstantsWithExpression::TYPE, 'constantArrayDereference']
        );
    }
}
