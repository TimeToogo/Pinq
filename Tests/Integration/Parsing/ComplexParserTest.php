<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

class ComplexParserTest extends ParserTest
{
    /**
     * @dataProvider parsers
     */
    public function testWithRelativeParameterInSignature()
    {
        $function =
                function (self $parameter) {
                    1;
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::value(1)]);

        $function =
                function (parent $parameter) {
                    3;
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::value(3)]);
    }
    /**
     * @dataProvider parsers
     */
    public function testWithRelativeParameterInBody()
    {
        $function =
                function () {
                    function (self $parameter) {};
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::closure(false, false, [O\Expression::parameter('parameter', '\self')], [], [])]);

        $function =
                function () {
                    function (parent $parameter) {};
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::closure(false, false, [O\Expression::parameter('parameter', '\parent')], [], [])]);
    }

    /**
     * @dataProvider parsers
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
     * @dataProvider parsers
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
     * @dataProvider parsers
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
     * @dataProvider parsers
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
     * @dataProvider parsers
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
     * @dataProvider parsers
     */
    public function testNestedClosures()
    {
        $function =
                function () {
                    return static function & ($foo) use ($foos, &$bar) {
                        $foo->bar += 5;
                    };
                };
        $this->assertParsedAs(
                $function,
                [O\Expression::returnExpression(O\Expression::closure(
                        true,
                        true,
                        [O\Expression::parameter('foo')],
                        [O\Expression::closureUsedVariable('foos'), O\Expression::closureUsedVariable('bar', true)],
                        [O\Expression::assign(
                                O\Expression::field(self::variable('foo'), O\Expression::value('bar')),
                                O\Operators\Assignment::ADDITION,
                                O\Expression::value(5))]))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testThatChainedMethodCalls()
    {
        $this->assertReturn(
                function (\Pinq\ITraversable $traversable) {
                    return $traversable->asArray();
                },
                O\Expression::methodCall(
                        O\Expression::variable(O\Expression::value('traversable')),
                        O\Expression::value('asArray')));

        $this->assertReturn(
                function (\Pinq\ITraversable $traversable) {
                    return $traversable
                            ->where(function ($i) { return $i > 0; })
                            ->all(function ($i) { return $i % 2 === 0; });
                },
                O\Expression::methodCall(
                        O\Expression::methodCall(
                                O\Expression::variable(O\Expression::value('traversable')),
                                O\Expression::value('where'),
                                [O\Expression::argument(O\Expression::closure(
                                        false,
                                        false,
                                        [O\Expression::parameter('i')],
                                        [],
                                        [O\Expression::returnExpression(O\Expression::binaryOperation(
                                                O\Expression::variable(O\Expression::value('i')),
                                                O\Operators\Binary::GREATER_THAN,
                                                O\Expression::value(0)))]))]),
                        O\Expression::value('all'),
                        [O\Expression::argument(O\Expression::closure(
                                false,
                                false,
                                [O\Expression::parameter('i')],
                                [],
                                [O\Expression::returnExpression(O\Expression::binaryOperation(
                                        O\Expression::binaryOperation(
                                                O\Expression::variable(O\Expression::value('i')),
                                                O\Operators\Binary::MODULUS,
                                                O\Expression::value(2)),
                                        O\Operators\Binary::IDENTITY,
                                        O\Expression::value(0)))]))]));
    }
}
