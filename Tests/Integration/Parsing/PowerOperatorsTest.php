<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

class PowerOperatorsTest extends ParserTest
{
    protected function setUp()
    {
        if (!version_compare(PHP_VERSION, '5.6.0-alpha3', '>=')) {
            $this->markTestSkipped('Requires >=PHP 5.6');
        }
    }

    /**
     * @dataProvider parsers
     */
    public function testBinaryOperator()
    {
        $this->assertParsedAs(
                [PowerOperators::TYPE, 'binaryOperator'],
                [
                        O\Expression::binaryOperation(
                                O\Expression::value(4),
                                O\Operators\Binary::POWER,
                                O\Expression::value(5)
                        )
                ]
        );
    }
}
