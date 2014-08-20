<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;

abstract class ProjectionBaseTest extends FunctionTest
{
    protected function functionWithEmptyReturnStatement()
    {
        return $this->buildFunction(
                '',
                __CLASS__,
                __NAMESPACE__,
                ['param' => 'scope'],
                [O\Expression::parameter('foo')],
                [
                        O\Expression::value('before'),
                        O\Expression::returnExpression(),
                        O\Expression::value('after'),
                        O\Expression::functionCall(O\Expression::value('boom'))
                ]
        );
    }

    public function testFunctionGetsReturnValueExpression()
    {
        /** @var $function Functions\ProjectionBase */
        $function = $this->functionWithReturnStatement();

        $this->assertSame(true, $function->hasReturnExpression());
        $this->assertEquals(
                O\Expression::returnExpression(O\Expression::value('return')),
                $function->getReturnExpression()
        );

        $this->assertSame(true, $function->hasReturnValueExpression());
        $this->assertEquals(
                O\Expression::value('return'),
                $function->getReturnValueExpression()
        );
    }

    public function testFunctionGetsReturnWithoutValueExpression()
    {
        /** @var $function Functions\ProjectionBase */
        $function = $this->functionWithEmptyReturnStatement();

        $this->assertSame(true, $function->hasReturnExpression());
        $this->assertEquals(
                O\Expression::returnExpression(),
                $function->getReturnExpression()
        );

        $this->assertSame(false, $function->hasReturnValueExpression());
        $this->assertEquals(null, $function->getReturnValueExpression());
    }
}
