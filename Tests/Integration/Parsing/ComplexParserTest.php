<?php 

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

class ComplexParserTest extends ParserTest
{
    /**
     * @dataProvider Parsers
     */
    public function testNestedVariableOperations()
    {
        $function = 
                function () {
                    $a += $b - $c / $d % $d;
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::assign(
                        self::variable('a'),
                        O\Operators\Assignment::ADDITION,
                        O\Expression::binaryOperation(
                                self::variable('b'),
                                O\Operators\Binary::SUBTRACTION,
                                O\Expression::binaryOperation(
                                        O\Expression::binaryOperation(
                                                self::variable('c'),
                                                O\Operators\Binary::DIVISION,
                                                self::variable('d')),
                                        O\Operators\Binary::MODULUS,
                                        self::variable('d'))))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testParenthesisedNestedVariableOperations()
    {
        $function = 
                function () {
                    $a >>= ($b - $c) / $d % $d;
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::assign(
                        self::variable('a'),
                        O\Operators\Assignment::SHIFT_RIGHT,
                        O\Expression::binaryOperation(
                                O\Expression::binaryOperation(
                                        O\Expression::binaryOperation(
                                                self::variable('b'),
                                                O\Operators\Binary::SUBTRACTION,
                                                self::variable('c')),
                                        O\Operators\Binary::DIVISION,
                                        self::variable('d')),
                                O\Operators\Binary::MODULUS,
                                self::variable('d')))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedUnaryOperators()
    {
        $function = 
                function () {
                    -+-+-+$i++;
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::unaryOperation(
                        O\Operators\Unary::NEGATION,
                        O\Expression::unaryOperation(
                                O\Operators\Unary::PLUS,
                                O\Expression::unaryOperation(
                                        O\Operators\Unary::NEGATION,
                                        O\Expression::unaryOperation(
                                                O\Operators\Unary::PLUS,
                                                O\Expression::unaryOperation(
                                                        O\Operators\Unary::NEGATION,
                                                        O\Expression::unaryOperation(
                                                                O\Operators\Unary::PLUS,
                                                                O\Expression::unaryOperation(
                                                                        O\Operators\Unary::INCREMENT,
                                                                        self::variable('i'))))))))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedCastOperators()
    {
        $function = 
                function () {
                    (double) (object) (bool) (int) (string) $i;
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::cast(
                        O\Operators\Cast::DOUBLE,
                        O\Expression::cast(
                                O\Operators\Cast::OBJECT,
                                O\Expression::cast(
                                        O\Operators\Cast::BOOLEAN,
                                        O\Expression::cast(
                                                O\Operators\Cast::INTEGER,
                                                O\Expression::cast(O\Operators\Cast::STRING, self::variable('i'))))))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedVariableTraversal()
    {
        $function = 
                function () {
                    $i->field->method()['index'];
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::index(
                        O\Expression::methodCall(
                                O\Expression::field(self::variable('i'), O\Expression::value('field')),
                                O\Expression::value('method')),
                        O\Expression::value('index'))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedClosures()
    {
        $function = 
                function () {
                    return function ($foo) {
                        $foo->bar += 5;
                    };
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::returnExpression(O\Expression::closure(
                        [O\Expression::parameter('foo')],
                        [],
                        [O\Expression::assign(
                                O\Expression::field(self::variable('foo'), O\Expression::value('bar')),
                                O\Operators\Assignment::ADDITION,
                                O\Expression::value(5))]))]);
    }
}