<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

class VariadicParameterTest extends ParserTest
{
    /**
     * @var VariadicParameters
     */
    protected $parameters;

    protected function setUp(): void
    {
        if (version_compare(PHP_VERSION, '5.6.0-alpha3', '>=')) {
            $this->parameters = new VariadicParameters();
        } else {
            $this->markTestSkipped('Requires >=PHP 5.6');
        }
    }

    /**
     * @dataProvider parsers
     */
    public function testSimpleVariadic()
    {
        $this->assertParsedAs(
                [$this->parameters, 'simpleVariadic'],
                [
                        O\Expression::closure(
                                false,
                                false,
                                [O\Expression::parameter('arguments', null, null, false, true)],
                                [],
                                []
                        )
                ]
        );
    }

    /**
     * @dataProvider parsers
     */
    public function testOnlyArraysByRef()
    {
        $this->assertParsedAs(
                [$this->parameters, 'onlyArraysByRef'],
                [
                        O\Expression::closure(
                                false,
                                false,
                                [O\Expression::parameter('arguments', 'array', null, true, true)],
                                [],
                                []
                        )
                ]
        );
    }

    /**
     * @dataProvider parsers
     */
    public function testArgumentUnpacking()
    {
        $this->assertParsedAs(
                [$this->parameters, 'argumentUnpacking'],
                [
                        O\Expression::functionCall(
                                O\Expression::value('func'),
                                [O\Expression::argument(O\Expression::arrayExpression([]), true)]
                        )
                ]
        );
    }
}
