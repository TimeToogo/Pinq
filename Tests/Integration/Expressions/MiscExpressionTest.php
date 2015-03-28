<?php

namespace Pinq\Tests\Integration\Expressions;

use Pinq\Expressions as O;

class MiscExpressionTest extends ExpressionTest
{
    public function testVariableExpressionSimplifiesCorrectly()
    {
        $this->assertEquals(
                O\Expression::value('1,2,3'),
                O\Expression::variable(O\Expression::value('var'))->simplify(
                        O\EvaluationContext::globalScope(null, ['var' => '1,2,3'])
                )
        );
    }

    public function testVariableSuperGlobalExpressionSimplifiesCorrectly()
    {
        $this->assertEquals(
                O\Expression::value($_POST),
                O\Expression::variable(O\Expression::value('_POST'))->simplify(
                        O\EvaluationContext::globalScope()
                )
        );
    }

    public function testVariablesCompileCorrectly()
    {
        $this->assertEquals(
                O\Expression::variable(O\Expression::value('var'))->compile(),
                '$var'
        );

        $this->assertEquals(
                O\Expression::variable(O\Expression::value('var1'))->compile(),
                '$var1'
        );

        $this->assertEquals(
                O\Expression::variable(O\Expression::value('var bar'))->compile(),
                '${\'var bar\'}'
        );

        $this->assertEquals(
                O\Expression::variable(O\Expression::value('var-bar'))->compile(),
                '${\'var-bar\'}'
        );

        $this->assertEquals(
                O\Expression::variable(O\Expression::value('1var'))->compile(),
                '${\'1var\'}'
        );
    }

    public function testExpressionNameType()
    {
        $this->assertSame(O\BinaryOperationExpression::getExpressionTypeName(), 'BinaryOperation');
        $this->assertSame(O\VariableExpression::getExpressionTypeName(), 'Variable');
        $this->assertSame(O\Expression::getExpressionTypeName(), '');
    }

    public function testNamedParameterExpressionAsVariableMethod()
    {
        $this->assertEquals(
                O\Expression::variable(O\Expression::value('foo')),
                O\Expression::parameter('foo')->asVariable());

        $this->assertEquals(
                O\Expression::variable(O\Expression::value('foobar')),
                O\Expression::closureUsedVariable('foobar')->asVariable());
    }

    public function testAssignmentToBinaryOperatorEquivalent()
    {
        foreach([O\Operators\Assignment::EQUAL, O\Operators\Assignment::EQUAL_REFERENCE] as $operatorThatShouldNotChange) {
            $assignment = O\Expression::assign(
                    O\Expression::variable(O\Expression::value('foo')),
                    $operatorThatShouldNotChange,
                    O\Expression::variable(O\Expression::value('bar'))
            );

            $this->assertSame($assignment, $assignment->toBinaryOperationEquivalent());
        }

        $assignment = O\Expression::assign(
                O\Expression::variable(O\Expression::value('foo')),
                O\Operators\Assignment::ADDITION,
                O\Expression::variable(O\Expression::value('bar'))
        );


        $this->assertEquals(O\Expression::assign(
                O\Expression::variable(O\Expression::value('foo')),
                O\Operators\Assignment::EQUAL,
                O\Expression::binaryOperation(
                        O\Expression::variable(O\Expression::value('foo')),
                        O\Operators\Binary::ADDITION,
                        O\Expression::variable(O\Expression::value('bar'))
                )
        ), $assignment->toBinaryOperationEquivalent());
    }
}
