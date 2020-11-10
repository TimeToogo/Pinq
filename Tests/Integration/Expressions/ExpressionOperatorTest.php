<?php

namespace Pinq\Tests\Integration\Expressions;


use Pinq\Expressions\Operators\Assignment;
use Pinq\Expressions\Operators\Binary;
use Pinq\Expressions\Operators\Cast;
use Pinq\Expressions\Operators\Unary;
use Pinq\PinqException;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionOperatorTest extends ExpressionTest
{
    public function testBinary()
    {
        $this->assertSame(true, Binary::doBinaryOperation(5, Binary::EQUALITY, '5'));
        $this->assertSame(false, Binary::doBinaryOperation(5, Binary::INEQUALITY, '5'));
        $this->assertSame(false, Binary::doBinaryOperation(5, Binary::IDENTITY, '5'));
        $this->assertSame(true, Binary::doBinaryOperation(5, Binary::NOT_IDENTICAL, '5'));
        $this->assertSame(25, Binary::doBinaryOperation(5, Binary::POWER, 2));
        $this->assertSame(3, Binary::doBinaryOperation(5, Binary::SUBTRACTION, 2));
        $this->assertSame(7, Binary::doBinaryOperation(5, Binary::ADDITION, 2));
        $this->assertSame(10, Binary::doBinaryOperation(5, Binary::MULTIPLICATION, 2));
        $this->assertSame(2.5, Binary::doBinaryOperation(5, Binary::DIVISION, 2));
        $this->assertSame(1, Binary::doBinaryOperation(5, Binary::MODULUS, 2));
        $this->assertSame(true, Binary::doBinaryOperation(new \stdClass(), Binary::IS_INSTANCE_OF, 'stdClass'));
        $this->assertSame(false, Binary::doBinaryOperation(new \stdClass(), Binary::IS_INSTANCE_OF, __CLASS__));
        $this->assertSame(false, Binary::doBinaryOperation(true, Binary::LOGICAL_AND, false));
        $this->assertSame(true, Binary::doBinaryOperation(true, Binary::LOGICAL_OR, false));
        $this->assertSame(true, Binary::doBinaryOperation(3, Binary::LESS_THAN, 4));
        $this->assertSame(false, Binary::doBinaryOperation(3, Binary::GREATER_THAN, 4));
        $this->assertSame(true, Binary::doBinaryOperation(3, Binary::LESS_THAN_OR_EQUAL_TO, 4));
        $this->assertSame(false, Binary::doBinaryOperation(3, Binary::GREATER_THAN_OR_EQUAL_TO, 4));
        $this->assertSame(3 & 4, Binary::doBinaryOperation(3, Binary::BITWISE_AND, 4));
        $this->assertSame(3 | 4, Binary::doBinaryOperation(3, Binary::BITWISE_OR, 4));
        $this->assertSame(3 ^ 4, Binary::doBinaryOperation(3, Binary::BITWISE_XOR, 4));
        $this->assertSame(3 << 4, Binary::doBinaryOperation(3, Binary::SHIFT_LEFT, 4));
        $this->assertSame(3 >> 4, Binary::doBinaryOperation(3, Binary::SHIFT_RIGHT, 4));
    }

    public function testInvalidBinary()
    {
        $this->expectException(PinqException::class);
        Binary::doBinaryOperation(null, '#', null);
    }

    public function testAssignment()
    {
        $this->assertSame(5, Assignment::doAssignment($ref, Assignment::EQUAL, 5));
        $this->assertSame(5, $ref);

        $this->assertSame(25, Assignment::doAssignment($ref, Assignment::MULTIPLICATION, 5));
        $this->assertSame(25, $ref);

        $this->assertSame(5, Assignment::doAssignment($ref, Assignment::DIVISION, 5));
        $this->assertSame(5, $ref);

        $this->assertSame(25, Assignment::doAssignment($ref, Assignment::POWER, 2));
        $this->assertSame(25, $ref);

        $this->assertSame(5, Assignment::doAssignment($ref, Assignment::SUBTRACTION, 20));
        $this->assertSame(5, $ref);

        $this->assertSame('5abc', Assignment::doAssignment($ref, Assignment::CONCATENATE, 'abc'));
        $this->assertSame('5abc', $ref);

        $ref = 5;
        $this->assertSame(5 | 10, Assignment::doAssignment($ref, Assignment::BITWISE_OR, 10));
        $this->assertSame(5 | 10, $ref);
    }

    public function testInvalidAssignment()
    {
        $this->expectException(PinqException::class);
        Assignment::doAssignment($var, Assignment::EQUAL_REFERENCE, '');
    }

    public function testUnaryOperations()
    {
        $four = 4;
        $this->assertSame(~4, Unary::doUnaryOperation(Unary::BITWISE_NOT, $four));
        $this->assertSame(-4, Unary::doUnaryOperation(Unary::NEGATION, $four));
        $this->assertSame(4, Unary::doUnaryOperation(Unary::PLUS, $four));
        $this->assertSame(!4, Unary::doUnaryOperation(Unary::NOT, $four));

        //Affects reference
        $this->assertSame(4, Unary::doUnaryOperation(Unary::INCREMENT, $four));
        $this->assertSame(5, $four);

        $four = 4;
        $this->assertSame(4, Unary::doUnaryOperation(Unary::DECREMENT, $four));
        $this->assertSame(3, $four);

        $four = 4;
        $this->assertSame(5, Unary::doUnaryOperation(Unary::PRE_INCREMENT, $four));
        $this->assertSame(5, $four);

        $four = 4;
        $this->assertSame(3, Unary::doUnaryOperation(Unary::PRE_DECREMENT, $four));
        $this->assertSame(3, $four);
    }

    public function testInvalidUnaryOperator()
    {
        $this->expectException(PinqException::class);
        Unary::doUnaryOperation('##', $var);
    }

    public function testCasts()
    {
        $this->assertSame([], Cast::doCast(Cast::ARRAY_CAST, new \stdClass()));
        $this->assertSame(false, Cast::doCast(Cast::BOOLEAN, '0'));
        $this->assertSame(0.1, Cast::doCast(Cast::DOUBLE, '0.1'));
        $this->assertSame(-1, Cast::doCast(Cast::INTEGER, '-1'));
        $this->assertEquals(new \stdClass(), Cast::doCast(Cast::OBJECT, []));
        $this->assertSame('2.3', Cast::doCast(Cast::STRING, 2.3));
    }

    public function testInvalidCasts()
    {
        $this->expectException(PinqException::class);
        Cast::doCast('addddsdsaf', null);
    }

    public function testAssignmentToBinaryOperator()
    {
        $this->assertSame(Binary::ADDITION, Assignment::toBinaryOperator(Assignment::ADDITION));
        $this->assertSame(Binary::CONCATENATION, Assignment::toBinaryOperator(Assignment::CONCATENATE));
        $this->assertSame(null, Assignment::toBinaryOperator(Assignment::EQUAL));
        $this->assertSame(null, Assignment::toBinaryOperator(Assignment::EQUAL_REFERENCE));
        $this->assertSame(null, Assignment::toBinaryOperator('no-such-operator'));
    }
}
