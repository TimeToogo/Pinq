<?php

namespace Pinq\Tests\Integration\Parsing;

use \Pinq\Parsing\IParser;
use \Pinq\Expressions as O;

abstract class ComplexParserTest extends ParserTest
{
    /**
     * @dataProvider Parsers
     */
    public function testNestedVariableOperations()
    {
        $Function = function () {
            $A += $B - $C / $D % $D;
        };
        $this->AssertParsedAs($Function, [O\Expression::Assign(self::Variable('A'), O\Operators\Assignment::Addition,
                O\Expression::BinaryOperation(
                        self::Variable('B'), 
                        O\Operators\Binary::Subtraction, 
                        O\Expression::BinaryOperation(
                                O\Expression::BinaryOperation(
                                        self::Variable('C'), 
                                        O\Operators\Binary::Division, 
                                        self::Variable('D')), 
                                O\Operators\Binary::Modulus, 
                                self::Variable('D'))))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testParenthesisedNestedVariableOperations()
    {
        $Function = function () {
            $A >>= ($B - $C) / $D % $D;
        };
        $this->AssertParsedAs($Function, [O\Expression::Assign(self::Variable('A'), O\Operators\Assignment::ShiftRight,
                O\Expression::BinaryOperation(
                        O\Expression::BinaryOperation(
                                O\Expression::BinaryOperation(
                                        self::Variable('B'), 
                                        O\Operators\Binary::Subtraction, 
                                        self::Variable('C')), 
                                O\Operators\Binary::Division, 
                                self::Variable('D')), 
                        O\Operators\Binary::Modulus, 
                        self::Variable('D')))]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedUnaryOperators()
    {
        $Function = function () {
            -+-+-+$I++;//This is valid php...
        };
        
        $this->AssertParsedAs($Function, [
                O\Expression::UnaryOperation(
                        O\Operators\Unary::Negation,
                        O\Expression::UnaryOperation(
                                O\Operators\Unary::Plus,
                                O\Expression::UnaryOperation(
                                        O\Operators\Unary::Negation,
                                        O\Expression::UnaryOperation(
                                                O\Operators\Unary::Plus,
                                                O\Expression::UnaryOperation(
                                                        O\Operators\Unary::Negation,
                                                        O\Expression::UnaryOperation(
                                                                O\Operators\Unary::Plus,
                                                                O\Expression::UnaryOperation(
                                                                        O\Operators\Unary::Increment,/*>>>>>>>*/
                                                                        self::Variable('I'))
                                                                )
                                                        )
                                                )
                                        )
                                )
                        )]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedVastOperators()
    {
        $Function = function () {
            (double)(object)(boolean)(int)(string)$I;//This is value php...
        };
        
        $this->AssertParsedAs($Function, [
                O\Expression::Cast(
                        O\Operators\Cast::Double,
                        O\Expression::Cast(
                                O\Operators\Cast::Object,
                                O\Expression::Cast(
                                        O\Operators\Cast::Boolean,
                                        O\Expression::Cast(
                                                O\Operators\Cast::Integer,                  
                                                O\Expression::Cast(
                                                        O\Operators\Cast::String,
                                                        self::Variable('I')
                                                        )
                                                )
                                        )
                                )
                        )]);
    }
    
    /**
     * @dataProvider Parsers
     */
    public function testNestedVariableTraversal()
    {
        $Function = function () {
            $I->Field->Method()['Index'];
        };
        $this->AssertParsedAs($Function, [
                O\Expression::Index(
                        O\Expression::MethodCall(
                                O\Expression::Field(
                                        self::Variable('I'), 
                                        O\Expression::Value('Field')), 
                                O\Expression::Value('Method')),
                        O\Expression::Value('Index'))]);
    }
}
