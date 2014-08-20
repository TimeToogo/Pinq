<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

function TestFunction()
{
    return 1.1;
}
define('Pinq\\Tests\\Integration\\Parsing\\TEST_FUNCTION', __NAMESPACE__ . '\\TestFunction');

class Test
{
    public static $method = [__CLASS__, 'Method'];

    public static function method()
    {
        return 1.1;
    }
}

class InvokableObject
{
    public function __invoke()
    {
        return 1.1;
    }
}

class FunctionTypesTest extends ParserTest
{
    /**
     * @dataProvider parsers
     */
    public function testClosure()
    {
        $this->assertParsedAs(
                function () {
                    return 1.1;
                },
                [O\Expression::returnExpression(O\Expression::value(1.1))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testFunction()
    {
        $this->assertParsedAs(
                TEST_FUNCTION,
                [O\Expression::returnExpression(O\Expression::value(1.1))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMethod()
    {
        $this->assertParsedAs(
                Test::$method,
                [O\Expression::returnExpression(O\Expression::value(1.1))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testInvokableObject()
    {
        $this->assertParsedAs(
                new InvokableObject(),
                [O\Expression::returnExpression(O\Expression::value(1.1))]);
    }
}
