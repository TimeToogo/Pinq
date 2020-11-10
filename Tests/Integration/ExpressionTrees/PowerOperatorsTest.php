<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

class PowerOperatorsTest extends InterpreterTest
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
    public function testPowerBinaryOperator()
    {
        $valueSet = [
                [5, 0],
                [5, 5],
                [2, 3],
                [6, 8]
        ];

        $this->assertRecompilesCorrectly([PowerOperators::TYPE, 'power'], $valueSet);
    }

    /**
     * @dataProvider interpreters
     */
    public function testOnlyArraysByRef()
    {
        $valueSet = [
                [5, 0],
                [5, 5],
                [2, 3],
                [6, 8]
        ];

        $this->assertRecompilesCorrectly([PowerOperators::TYPE, 'square'], $valueSet);
    }
}
