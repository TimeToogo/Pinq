<?php 

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Parsing\IParser;
use Pinq\Expressions as O;
function TestFunction()
{
    return 1.1;
}
define('TEST_FUNCTION', __NAMESPACE__ . '\\TestFunction');

class Test
{
    public static $method = [__CLASS__, 'Method'];
    
    public static function method()
    {
        return 1.1;
    }
}

class FunctionTypesTest extends ParserTest
{
    /**
     * @dataProvider Parsers
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
     * @dataProvider Parsers
     */
    public function testFunction()
    {
        $this->assertParsedAs(
                TEST_FUNCTION,
                [O\Expression::returnExpression(O\Expression::value(1.1))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testMethod()
    {
        $this->assertParsedAs(
                Test::$method,
                [O\Expression::returnExpression(O\Expression::value(1.1))]);
    }
}