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
}
