<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

class ConstantArrayDereferenceTest extends ParserTest
{
    protected function setUp(): void
    {
        if (!version_compare(PHP_VERSION, '5.6.0-RC4', '>=')) {
            $this->markTestSkipped('Requires >=PHP 5.6');
        }
    }

    /**
     * @dataProvider parsers
     */
    public function testClassConstantArrayDereference()
    {
        $this->assertParsedAs(
                [ConstantArrayDereference::TYPE, 'classConstantArrayDereference'],
                [
                        O\Expression::Index(
                                O\Expression::classConstant(
                                        O\Expression::value('\\' . ConstantArrayDereference::TYPE),
                                        'SOME_ARRAY'
                                ),
                                O\Expression::value('foo')
                        )
                ]
        );
    }

    /**
     * @dataProvider parsers
     */
    public function testConstantArrayDereference()
    {
        $this->assertParsedAs(
                [ConstantArrayDereference::TYPE, 'constantArrayDereference'],
                [
                        O\Expression::Index(
                                O\Expression::constant('SOME_ARRAY'),
                                O\Expression::value('foo')
                        )
                ]
        );
    }
}
