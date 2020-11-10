<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Parsing\IParser;
use Pinq\Expressions as O;
use Pinq\Parsing\InvalidFunctionException;

class SimpleParserTest extends ParserTest
{
    /**
     * @dataProvider parsers
     */
    public function testEmptyFunction()
    {
        $this->assertParsedAs(function () {

        }, []);
    }

    /**
     * @dataProvider parsers
     */
    public function testValue()
    {
        $this->assertParsedAs(
                function () {
                    1;
                },
                [O\Expression::value(1)]);
    }

    /**
     * @dataProvider parsers
     */
    public function testReturnStatement()
    {
        $this->assertParsedAs(
                function () {
                    return;
                },
                [O\Expression::returnExpression()]);
    }

    /**
     * @dataProvider parsers
     */
    public function testThrowNewExceptionStatement()
    {
        $this->assertParsedAs(
                function () {
                    throw null;
                },
                [O\Expression::throwExpression(O\Expression::constant('null'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testReturnValueStatement()
    {
        $this->assertParsedAs(
                function () {
                    return 1;
                },
                [O\Expression::returnExpression(O\Expression::value(1))]);
        $this->assertParsedAs(
                function () {
                    return null;
                },
                [O\Expression::returnExpression(O\Expression::constant('null'))]);
        $this->assertParsedAs(
                function () {
                    return '';
                },
                [O\Expression::returnExpression(O\Expression::value(''))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testVariable()
    {
        $this->assertParsedAs(
                function () {
                    $i;
                },
                [O\Expression::variable(O\Expression::value('i'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testEmpty()
    {
        $this->assertParsedAs(
                function () {
                    empty($i);
                },
                [O\Expression::emptyExpression(O\Expression::variable(O\Expression::value('i')))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testIsset()
    {
        $this->assertParsedAs(
                function () {
                    isset($i);
                },
                [O\Expression::issetExpression([O\Expression::variable(O\Expression::value('i'))])]);
    }

    /**
     * @dataProvider parsers
     */
    public function testUnset()
    {
        $this->assertParsedAs(
                function () {
                    unset($i);
                },
                [O\Expression::unsetExpression([O\Expression::variable(O\Expression::value('i'))])]);
    }

    /**
     * @dataProvider parsers
     */
    public function testFunctionCall()
    {
        $this->assertParsedAs(
                function () {
                    func();
                },
                [O\Expression::functionCall(O\Expression::value('func'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testStaticMethodCall()
    {
        $this->assertParsedAs(
                function () {
                    \Object::method();
                    Object::method();
                },
                [O\Expression::staticMethodCall(
                        O\Expression::value('\\Object'),
                        O\Expression::value('method')),
                 O\Expression::staticMethodCall(
                         O\Expression::value('\\' . __NAMESPACE__ . '\\Object'),
                         O\Expression::value('method'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testStaticField()
    {
        $this->assertParsedAs(
                function () {
                    \Object::$field;
                    Object::$field;
                },
                [O\Expression::staticField(
                         O\Expression::value('\\Object'),
                         O\Expression::value('field')),
                 O\Expression::staticField(
                         O\Expression::value('\\' . __NAMESPACE__ . '\\Object'),
                         O\Expression::value('field'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testArray()
    {
        $this->assertParsedAs(
                function () {
                    [1 => 2];
                },
                [O\Expression::arrayExpression(
                        [O\Expression::arrayItem(O\Expression::value(1), O\Expression::value(2), false)])]);
    }

    /**
     * @dataProvider parsers
     */
    public function testReturnValues()
    {
        $this->assertReturn(function () { return 1; }, O\Expression::value(1));

        $this->assertReturn(function () { return 2; }, O\Expression::value(2));

        $this->assertReturn(function () { return '1'; }, O\Expression::value('1'));

        $this->assertReturn(function () { return 1.01; }, O\Expression::value(1.01));

        $this->assertReturn(function () { return true; }, O\Expression::constant('true'));

        $this->assertReturn(function () { return null; }, O\Expression::constant('null'));

        $this->assertReturn(function () { return [1, 5, 57, 4 => 3, 'tset' => 'ftest', true => &$foo]; }, O\Expression::arrayExpression(
                        [O\Expression::arrayItem(null, O\Expression::value(1), false),
                            O\Expression::arrayItem(null, O\Expression::value(5), false),
                            O\Expression::arrayItem(null, O\Expression::value(57), false),
                            O\Expression::arrayItem(O\Expression::value(4), O\Expression::value(3), false),
                            O\Expression::arrayItem(O\Expression::value('tset'), O\Expression::value('ftest'), false),
                            O\Expression::arrayItem(O\Expression::constant('true'), O\Expression::variable(O\Expression::value('foo')), true)]));

        $this->assertReturn(function () { return new \stdClass(); }, O\Expression::newExpression(O\Expression::value('\\stdClass')));

    }

    /**
     * @dataProvider parsers
     */
    public function testInternalFunctionIsRejected(IParser $parser)
    {
        $this->expectException(InvalidFunctionException::class);
        $parser->parse($parser->getReflection('strlen'));
    }

    /**
     * @dataProvider parsers
     */
    public function testEvaledFunctionIsRejected(IParser $parser)
    {
        $this->expectException(InvalidFunctionException::class);
        $evaledFunction = eval('return function () {};');
        $parser->parse($parser->getReflection($evaledFunction));
    }

    // <editor-fold defaultstate="collapsed" desc="Value traversals">
    /**
     * @dataProvider parsers
     */
    public function testField()
    {
        $this->assertParsedAs(
                function () {
                    $i->field;
                },
                [O\Expression::field(
                        O\Expression::variable(O\Expression::value('i')),
                        O\Expression::value('field'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMethodCall()
    {
        $this->assertParsedAs(
                function () {
                    $i->method();
                },
                [O\Expression::methodCall(
                        O\Expression::variable(O\Expression::value('i')),
                        O\Expression::value('method'))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testIndex()
    {
        $this->assertParsedAs(
                function () {
                    $i[0];
                },
                [O\Expression::index(
                        O\Expression::variable(O\Expression::value('i')),
                        O\Expression::value(0))]);

        $this->assertParsedAs(
                function () {
                    //Must use write context
                    f($i[]);
                },
                [
                        O\Expression::functionCall(
                                O\Expression::value('f'),
                                [O\Expression::argument(O\Expression::index(O\Expression::variable(O\Expression::value('i'))))]
                        )
                ]
        );
    }

    /**
     * @dataProvider parsers
     */
    public function testInvocation()
    {
        $this->assertParsedAs(
                function () {
                    $i();
                },
                [O\Expression::invocation(O\Expression::variable(O\Expression::value('i')))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testTernary()
    {
        $this->assertParsedAs(
                function () {
                    true ? true : false;
                },
                [O\Expression::ternary(
                         O\Expression::constant('true'),
                         O\Expression::constant('true'),
                         O\Expression::constant('false'))]);
        $this->assertParsedAs(
                function () {
                    true ? : false;
                },
                [O\Expression::ternary(
                         O\Expression::constant('true'),
                         null,
                         O\Expression::constant('false'))]);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Binary Operators">

    /**
     * @dataProvider parsers
     */
    public function testMathBinaryOperations()
    {
        $this->assertBinaryOperation(function () { 1 + 1; }, 1, O\Operators\Binary::ADDITION, 1);
        $this->assertBinaryOperation(function () { 1 - 1; }, 1, O\Operators\Binary::SUBTRACTION, 1);
        $this->assertBinaryOperation(function () { 1 * 1; }, 1, O\Operators\Binary::MULTIPLICATION, 1);
        $this->assertBinaryOperation(function () { 1 / 1; }, 1, O\Operators\Binary::DIVISION, 1);
        $this->assertBinaryOperation(function () { 1 % 1; }, 1, O\Operators\Binary::MODULUS, 1);
    }

    /**
     * @dataProvider parsers
     */
    private function assertBinaryOperation(callable $function, $left, $operator, $right)
    {
        $this->assertParsedAs(
                $function,
                [O\Expression::binaryOperation(
                        O\Expression::value($left),
                        $operator,
                        O\Expression::value($right))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testLogicalBinaryOperations()
    {
        $this->assertBinaryOperation(function () { 'true' && 'true'; }, 'true', O\Operators\Binary::LOGICAL_AND, 'true');
        $this->assertBinaryOperation(function () { 'true' || 'true'; }, 'true', O\Operators\Binary::LOGICAL_OR, 'true');
    }

    /**
     * @dataProvider parsers
     */
    public function testComparisonBinaryOperations()
    {
        $this->assertBinaryOperation(function () { 1 === 1; }, 1, O\Operators\Binary::IDENTITY, 1);
        $this->assertBinaryOperation(function () { 1 !== 1; }, 1, O\Operators\Binary::NOT_IDENTICAL, 1);
        $this->assertBinaryOperation(function () { 1 == 1; }, 1, O\Operators\Binary::EQUALITY, 1);
        $this->assertBinaryOperation(function () { 1 != 1; }, 1, O\Operators\Binary::INEQUALITY, 1);
        $this->assertBinaryOperation(function () { 1 > 1; }, 1, O\Operators\Binary::GREATER_THAN, 1);
        $this->assertBinaryOperation(function () { 1 >= 1; }, 1, O\Operators\Binary::GREATER_THAN_OR_EQUAL_TO, 1);
        $this->assertBinaryOperation(function () { 1 < 1; }, 1, O\Operators\Binary::LESS_THAN, 1);
        $this->assertBinaryOperation(function () { 1 <= 1; }, 1, O\Operators\Binary::LESS_THAN_OR_EQUAL_TO, 1);
    }

    /**
     * @dataProvider parsers
     */
    public function testBitwiseBinaryOperations()
    {
        $this->assertBinaryOperation(function () { 1 & 1; }, 1, O\Operators\Binary::BITWISE_AND, 1);
        $this->assertBinaryOperation(function () { 1 | 1; }, 1, O\Operators\Binary::BITWISE_OR, 1);
        $this->assertBinaryOperation(function () { 1 ^ 1; }, 1, O\Operators\Binary::BITWISE_XOR, 1);
        $this->assertBinaryOperation(function () { 1 >> 1; }, 1, O\Operators\Binary::SHIFT_RIGHT, 1);
        $this->assertBinaryOperation(function () { 1 << 1; }, 1, O\Operators\Binary::SHIFT_LEFT, 1);
    }

    /**
     * @dataProvider parsers
     */
    public function testStringBinaryOperations()
    {
        $this->assertBinaryOperation(function () { 1 . 1; }, 1, O\Operators\Binary::CONCATENATION, 1);
    }

    /**
     * @dataProvider parsers
     */
    public function testInstanceOfBinaryOperation()
    {
        $this->assertParsedAs(
                function () { $this instanceof \stdClass; },
                [O\Expression::binaryOperation(
                        O\Expression::variable(O\Expression::value('this')),
                        O\Operators\Binary::IS_INSTANCE_OF,
                        O\Expression::value('\\stdClass'))]);

        $this->assertParsedAs(
                function () { $this instanceof $bar; },
                [O\Expression::binaryOperation(
                        O\Expression::variable(O\Expression::value('this')),
                        O\Operators\Binary::IS_INSTANCE_OF,
                        O\Expression::variable(O\Expression::value('bar')))]);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Unary Operators">

    /**
     * @dataProvider parsers
     */
    public function testMathUnaryOperations()
    {
        $this->assertUnaryOperation(function () { $i++; }, O\Operators\Unary::INCREMENT, 'i');
        $this->assertUnaryOperation(function () { $i--; }, O\Operators\Unary::DECREMENT, 'i');
        $this->assertUnaryOperation(function () { ++$i; }, O\Operators\Unary::PRE_INCREMENT, 'i');
        $this->assertUnaryOperation(function () { --$i; }, O\Operators\Unary::PRE_DECREMENT, 'i');
        $this->assertUnaryOperation(function () { +$i; }, O\Operators\Unary::PLUS, 'i');
        $this->assertUnaryOperation(function () { -$i; }, O\Operators\Unary::NEGATION, 'i');
    }

    /**
     * @dataProvider parsers
     */
    private function assertUnaryOperation(callable $function, $operator, $operandName)
    {
        $this->assertParsedAs(
                $function,
                [O\Expression::unaryOperation(
                        $operator,
                        O\Expression::variable(O\Expression::value($operandName)))]);
    }

    /**
     * @dataProvider parsers
     */
    public function testBitwiseUnaryOperations()
    {
        $this->assertUnaryOperation(
                function () {
                    ~$i;
                },
                O\Operators\Unary::BITWISE_NOT,
                'i');
    }

    /**
     * @dataProvider parsers
     */
    public function testLogicalUnaryOperations()
    {
        $this->assertUnaryOperation(
                function () {
                    !$i;
                },
                O\Operators\Unary::NOT,
                'i');
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Assignment Operators">

    /**
     * @dataProvider parsers
     */
    public function testNormalAssignmentOperations()
    {
        $this->assertAssignment(function () { $l = $r; }, 'l', O\Operators\Assignment::EQUAL, 'r');
        $this->assertAssignment(function () { $l =& $r; }, 'l', O\Operators\Assignment::EQUAL_REFERENCE, 'r');
    }

    /**
     * @dataProvider parsers
     */
    private function assertAssignment(callable $function, $assignToName, $operator, $assigmentName)
    {
        $this->assertParsedAs(
                $function,
                [O\Expression::assign(
                        O\Expression::variable(O\Expression::value($assignToName)),
                        $operator,
                        O\Expression::variable(O\Expression::value($assigmentName)))]);
    }

    /**
     * @dataProvider parsers
     */
    public function tesMathAssignmentOperations()
    {
        $this->assertAssignment(function () { $l += $r; }, 'l', O\Operators\Assignment::ADDITION, 'r');
        $this->assertAssignment(function () { $l -= $r; }, 'l', O\Operators\Assignment::SUBTRACTION, 'r');
        $this->assertAssignment(function () { $l *= $r; }, 'l', O\Operators\Assignment::MULTIPLICATION, 'r');
        $this->assertAssignment(function () { $l /= $r; }, 'l', O\Operators\Assignment::DIVISION, 'r');
        $this->assertAssignment(function () { $l %= $r; }, 'l', O\Operators\Assignment::MODULUS, 'r');
    }

    /**
     * @dataProvider parsers
     */
    public function tesBitwiseAssignmentOperations()
    {
        $this->assertAssignment(function () { $l &= $r; }, 'l', O\Operators\Assignment::BITWISE_AND, 'r');
        $this->assertAssignment(function () { $l |= $r; }, 'l', O\Operators\Assignment::BITWISE_OR, 'r');
        $this->assertAssignment(function () { $l ^= $r; }, 'l', O\Operators\Assignment::BITWISE_XOR, 'r');
        $this->assertAssignment(function () { $l <<= $r; }, 'l', O\Operators\Assignment::SHIFT_LEFT, 'r');
        $this->assertAssignment(function () { $l >>= $r; }, 'l', O\Operators\Assignment::SHIFT_RIGHT, 'r');
    }

    /**
     * @dataProvider parsers
     */
    public function testStringAssignmentOperations()
    {
        $this->assertAssignment(function () { $l .= $r; }, 'l', O\Operators\Assignment::CONCATENATE, 'r');
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Cast Operators">

    /**
     * @dataProvider parsers
     */
    public function testCastOperators()
    {
        $this->assertCast(function () { (string) $i; }, O\Operators\Cast::STRING, 'i');
        $this->assertCast(function () { (int) $i; }, O\Operators\Cast::INTEGER, 'i');
        $this->assertCast(function () { (integer) $i; }, O\Operators\Cast::INTEGER, 'i');
        $this->assertCast(function () { (double) $i; }, O\Operators\Cast::DOUBLE, 'i');
        $this->assertCast(function () { (float) $i; }, O\Operators\Cast::DOUBLE, 'i');
        $this->assertCast(function () { (object) $i; }, O\Operators\Cast::OBJECT, 'i');
        $this->assertCast(function () { (array) $i; }, O\Operators\Cast::ARRAY_CAST, 'i');
    }

    /**
     * @dataProvider parsers
     */
    private function assertCast(callable $function, $typeOperator, $castName)
    {
        $this->assertParsedAs(
                $function,
                [O\Expression::cast(
                        $typeOperator,
                        O\Expression::variable(O\Expression::value($castName)))]);
    }
    // </editor-fold>
}
