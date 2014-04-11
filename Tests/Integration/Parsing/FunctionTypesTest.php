<?php

namespace Pinq\Tests\Integration\Parsing;

use \Pinq\Parsing\IParser;
use \Pinq\Expressions as O;


function TestFunction () { return 1.1; }

define('TEST_FUNCTION', __NAMESPACE__ . '\\TestFunction');

class Test 
{
    public static $Method = [__CLASS__, 'Method'];
    
    public static function Method () { return 1.1; }
}

class FunctionTypesTest extends ParserTest
{
    /**
     * @dataProvider Parsers
     */
    public function testClosure() 
    {
        $this->AssertParsedAs(function () { return 1.1; }, [O\Expression::ReturnExpression(O\Expression::Value(1.1))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testFunction() 
    {
        $this->AssertParsedAs(TEST_FUNCTION, [O\Expression::ReturnExpression(O\Expression::Value(1.1))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testMethod() 
    {
        $this->AssertParsedAs(Test::$Method, [O\Expression::ReturnExpression(O\Expression::Value(1.1))]);
    }
    
}