<?php

namespace Pinq\Tests\Integration\Parsing;

use \Pinq\Parsing\IParser;
use \Pinq\Expressions as O;

class SimpleParserTest extends ParserTest
{
    /**
     * @dataProvider Parsers
     */
    public function testEmptyFunction() 
    {
        $this->AssertParsedAs(function () {}, []);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testValue() 
    {
        $this->AssertParsedAs(function () { 1; }, [O\Expression::Value(1)]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testReturnStatment() 
    {
        $this->AssertParsedAs(function () { return; }, [O\Expression::ReturnExpression()]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testThrowNewExceptionStatment() 
    {
        $this->AssertParsedAs(function () { throw null; }, [O\Expression::ThrowExpression(O\Expression::Value(null))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testReturnValueStatment() 
    {
        $this->AssertParsedAs(function () { return 1; }, [O\Expression::ReturnExpression(O\Expression::Value(1))]);
        $this->AssertParsedAs(function () { return null; }, [O\Expression::ReturnExpression(O\Expression::Value(null))]);
        $this->AssertParsedAs(function () { return ''; }, [O\Expression::ReturnExpression(O\Expression::Value(''))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testVariable() 
    {
        $this->AssertParsedAs(function () { $I; }, [O\Expression::Variable(O\Expression::Value('I'))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testEmpty() 
    {
        $this->AssertParsedAs(function () { empty($I); }, [O\Expression::EmptyExpression(O\Expression::Variable(O\Expression::Value('I')))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testIsset() 
    {
        $this->AssertParsedAs(function () { isset($I); }, [O\Expression::IssetExpression([O\Expression::Variable(O\Expression::Value('I'))])]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testFunctionCall() 
    {
        $this->AssertParsedAs(function () { func(); }, [O\Expression::FunctionCall(O\Expression::Value('func'))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testStaticMethodCall() 
    {
        $this->AssertParsedAs(function () { \Object::Method(); }, [O\Expression::StaticMethodCall(O\Expression::Value('Object'), O\Expression::Value('Method'))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testArray() 
    {
        $this->AssertParsedAs(function () { [1 => 2]; }, [O\Expression::ArrayExpression(
                [O\Expression::Value(1)], 
                [O\Expression::Value(2)])]);
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException \Pinq\Parsing\InvalidFunctionException
     */
    public function testInternalFunctionIsRejected(IParser $Parser) 
    {
        $Parser->Parse(new \ReflectionFunction('strlen'));
    }
    
    /**
     * @dataProvider Parsers
     * @expectedException \Pinq\Parsing\InvalidFunctionException
     */
    public function testEvaledFunctionIsRejected(IParser $Parser) 
    {
        $EvaledFunction = eval('return function () {};');
        $Parser->Parse(new \ReflectionFunction($EvaledFunction));
    }
    
    // <editor-fold defaultstate="collapsed" desc="Value traversals">
    
    /**
     * @dataProvider Parsers
     */
    public function testField() 
    {
        $this->AssertParsedAs(function () { $I->Field; }, [O\Expression::Field(
                O\Expression::Variable(O\Expression::Value('I')),
                O\Expression::Value('Field'))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testMethodCall() 
    {
        $this->AssertParsedAs(function () { $I->Method(); }, [O\Expression::MethodCall(
                O\Expression::Variable(O\Expression::Value('I')),
                O\Expression::Value('Method'))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testIndex() 
    {
        $this->AssertParsedAs(function () { $I[0]; }, [O\Expression::Index(
                O\Expression::Variable(O\Expression::Value('I')),
                O\Expression::Value(0))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testInvocation() 
    {
        $this->AssertParsedAs(function () { $I(); }, [O\Expression::Invocation(
                O\Expression::Variable(O\Expression::Value('I')))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testTernary() 
    {
        $this->AssertParsedAs(function () { true ? true : false; }, [O\Expression::Ternary(
                O\Expression::Value(true),
                O\Expression::Value(true),
                O\Expression::Value(false))]);
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Binary Operators">
    
    /**
     * @dataProvider Parsers
     */
    private function AssertBinaryOperation(callable $Function, $Left, $Operator, $Right) 
    {
        $this->AssertParsedAs($Function, 
                [O\Expression::BinaryOperation(
                        O\Expression::Value($Left),
                        $Operator,
                        O\Expression::Value($Right))
                ]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testMathBinaryOperations()
    {
        $this->AssertBinaryOperation(function () { 1 + 1; }, 1, O\Operators\Binary::Addition, 1);
        $this->AssertBinaryOperation(function () { 1 - 1; }, 1, O\Operators\Binary::Subtraction, 1);
        $this->AssertBinaryOperation(function () { 1 * 1; }, 1, O\Operators\Binary::Multiplication, 1);
        $this->AssertBinaryOperation(function () { 1 / 1; }, 1, O\Operators\Binary::Division, 1);
        $this->AssertBinaryOperation(function () { 1 % 1; }, 1, O\Operators\Binary::Modulus, 1);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testLogicalBinaryOperations()
    {
        $this->AssertBinaryOperation(function () { true && true; }, true, O\Operators\Binary::LogicalAnd, true);
        $this->AssertBinaryOperation(function () { true || true; }, true, O\Operators\Binary::LogicalOr, true);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testComparisonBinaryOperations()
    {
        $this->AssertBinaryOperation(function () { 1 === 1; }, 1, O\Operators\Binary::Identity, 1);
        $this->AssertBinaryOperation(function () { 1 == 1; }, 1, O\Operators\Binary::Equality, 1);
        $this->AssertBinaryOperation(function () { 1 !== 1; }, 1, O\Operators\Binary::NotIdentical, 1);
        $this->AssertBinaryOperation(function () { 1 > 1; }, 1, O\Operators\Binary::GreaterThan, 1);
        $this->AssertBinaryOperation(function () { 1 >= 1; }, 1, O\Operators\Binary::GreaterThanOrEqualTo, 1);
        $this->AssertBinaryOperation(function () { 1 < 1; }, 1, O\Operators\Binary::LessThan, 1);
        $this->AssertBinaryOperation(function () { 1 <= 1; }, 1, O\Operators\Binary::LessThanOrEqualTo, 1);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testBitwiseBinaryOperations()
    {
        $this->AssertBinaryOperation(function () { 1 & 1; }, 1, O\Operators\Binary::BitwiseAnd, 1);
        $this->AssertBinaryOperation(function () { 1 | 1; }, 1, O\Operators\Binary::BitwiseOr, 1);
        $this->AssertBinaryOperation(function () { 1 ^ 1; }, 1, O\Operators\Binary::BitwiseXor, 1);
        $this->AssertBinaryOperation(function () { 1 >> 1; }, 1, O\Operators\Binary::ShiftRight, 1);
        $this->AssertBinaryOperation(function () { 1 >> 1; }, 1, O\Operators\Binary::ShiftRight, 1);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testStringBinaryOperations()
    {
        $this->AssertBinaryOperation(function () { '1' . '1'; }, '1', O\Operators\Binary::Concatenation, '1');
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Unary Operators">
    
    /**
     * @dataProvider Parsers
     */
    private function AssertUnaryOperation(callable $Function, $Operator, $OperandName) 
    {
        $this->AssertParsedAs($Function, 
                [O\Expression::UnaryOperation(
                        $Operator,
                        O\Expression::Variable(O\Expression::Value($OperandName)))
                ]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testMathUnaryOperations()
    {
        $this->AssertUnaryOperation(function () { $I++; }, O\Operators\Unary::Increment, 'I');
        $this->AssertUnaryOperation(function () { $I--; }, O\Operators\Unary::Decrement, 'I');
        $this->AssertUnaryOperation(function () { ++$I; }, O\Operators\Unary::PreIncrement, 'I');
        $this->AssertUnaryOperation(function () { --$I; }, O\Operators\Unary::PreDecrement, 'I');
        $this->AssertUnaryOperation(function () { +$I; }, O\Operators\Unary::Plus, 'I');
        $this->AssertUnaryOperation(function () { -$I; }, O\Operators\Unary::Negation, 'I');
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testBitwiseUnaryOperations()
    {
        $this->AssertUnaryOperation(function () { ~$I; }, O\Operators\Unary::BitwiseNot, 'I');
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testLogicalUnaryOperations()
    {
        $this->AssertUnaryOperation(function () { !$I; }, O\Operators\Unary::Not, 'I');
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Assignment Operators">
    
    /**
     * @dataProvider Parsers
     */
    private function AssertAssignment(callable $Function, $AssignToName, $Operator, $AssigmentName) 
    {
        $this->AssertParsedAs($Function, 
                [O\Expression::Assign(
                        O\Expression::Variable(O\Expression::Value($AssignToName)),
                        $Operator,
                        O\Expression::Variable(O\Expression::Value($AssigmentName)))
                ]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNormalAssignmentOperations()
    {
        $this->AssertAssignment(function () { $L = $R; }, 'L', O\Operators\Assignment::Equal, 'R');
        $this->AssertAssignment(function () { $L =& $R; }, 'L', O\Operators\Assignment::EqualReference, 'R');
    }
    
    /**
     * @dataProvider Parsers
     */
    public function tesMathAssignmentOperations()
    {
        $this->AssertAssignment(function () { $L += $R; }, 'L', O\Operators\Assignment::Addition, 'R');
        $this->AssertAssignment(function () { $L -= $R; }, 'L', O\Operators\Assignment::Subtraction, 'R');
        $this->AssertAssignment(function () { $L *= $R; }, 'L', O\Operators\Assignment::Multiplication, 'R');
        $this->AssertAssignment(function () { $L /= $R; }, 'L', O\Operators\Assignment::Division, 'R');
        $this->AssertAssignment(function () { $L %= $R; }, 'L', O\Operators\Assignment::Modulus, 'R');
    }
    
    /**
     * @dataProvider Parsers
     */
    public function tesBitwiseAssignmentOperations()
    {
        $this->AssertAssignment(function () { $L &= $R; }, 'L', O\Operators\Assignment::BitwiseAnd, 'R');
        $this->AssertAssignment(function () { $L |= $R; }, 'L', O\Operators\Assignment::BitwiseOr, 'R');
        $this->AssertAssignment(function () { $L ^= $R; }, 'L', O\Operators\Assignment::BitwiseXor, 'R');
        $this->AssertAssignment(function () { $L <<= $R; }, 'L', O\Operators\Assignment::ShiftLeft, 'R');
        $this->AssertAssignment(function () { $L >>= $R; }, 'L', O\Operators\Assignment::ShiftRight, 'R');
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testStringAssignmentOperations()
    {
        $this->AssertAssignment(function () { $L .= $R; }, 'L', O\Operators\Assignment::Concatenate, 'R');
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Cast Operators">
    
    /**
     * @dataProvider Parsers
     */
    private function AssertCast(callable $Function, $TypeOperator, $CastName) 
    {
        $this->AssertParsedAs($Function, 
                [O\Expression::Cast(
                        $TypeOperator,
                        O\Expression::Variable(O\Expression::Value($CastName)))
                ]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testCastOperators()
    {
        $this->AssertCast(function () { (string)$I; }, O\Operators\Cast::String, 'I');
        $this->AssertCast(function () { (int)$I; }, O\Operators\Cast::Integer, 'I');
        $this->AssertCast(function () { (integer)$I; }, O\Operators\Cast::Integer, 'I');
        $this->AssertCast(function () { (double)$I; }, O\Operators\Cast::Double, 'I');
        $this->AssertCast(function () { (float)$I; }, O\Operators\Cast::Double, 'I');
        $this->AssertCast(function () { (bool)$I; }, O\Operators\Cast::Boolean, 'I');
        $this->AssertCast(function () { (boolean)$I; }, O\Operators\Cast::Boolean, 'I');
        $this->AssertCast(function () { (object)$I; }, O\Operators\Cast::Object, 'I');
    }
    
    // </editor-fold>
}
