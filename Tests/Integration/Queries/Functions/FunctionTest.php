<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;
use Pinq\Tests\PinqTestCase;

abstract class FunctionTest extends PinqTestCase
{
    /**
     * @return callable
     */
    abstract protected function functionFactory();

    /**
     * @param string $callableParameter
     * @param string $scopeType
     * @param array  $parameterScopedVariableMap
     * @param array  $parameterExpressions
     * @param array  $bodyExpressions
     *
     * @return Functions\Base
     */
    protected function buildFunction(
            $callableParameter,
            $scopeType = null,
            array $parameterScopedVariableMap = [],
            array $parameterExpressions = [],
            array $bodyExpressions = null
    ) {
        $factory = $this->functionFactory();

        return $factory(
                $callableParameter,
                $scopeType,
                $parameterScopedVariableMap,
                $parameterExpressions,
                $bodyExpressions
        );
    }

    protected function emptyFunction()
    {
        return $this->buildFunction('CALLABLE!!', null, [], [], []);
    }

    protected function internalFunction()
    {
        return $this->buildFunction('CALLABLE!!', null, [], [O\Expression::parameter('str')], null);
    }

    protected function functionWithReturnStatement()
    {
        return $this->buildFunction(
                '',
                __CLASS__,
                ['param' => 'scope'],
                [O\Expression::parameter('foo')],
                [
                        O\Expression::value('before'),
                        O\Expression::returnExpression(O\Expression::value('return')),
                        O\Expression::value('after'),
                        O\Expression::functionCall(O\Expression::value('boom'))
                ]
        );
    }

    public function testEmptyFunction()
    {
        $function = $this->emptyFunction();

        $this->assertSame('CALLABLE!!', $function->getCallableId());
        $this->assertSame(0, $function->getParameters()->count());
        $this->assertEquals([], $function->getParameters()->getAll());
        $this->assertEquals([], $function->getParameterScopedVariableMap());
        $this->assertSame(null, $function->getScopeType());
        $this->assertSame(false, $function->hasScopeType());
        $this->assertSame(0, $function->countBodyExpressions());
        $this->assertSame(0, $function->countBodyExpressionsUntilReturn());
        $this->assertEquals([], $function->getBodyExpressions());
        $this->assertEquals([], $function->getBodyExpressionsUntilReturn());
    }

    public function testFunctionWithReturnStatement()
    {
        $function = $this->functionWithReturnStatement();

        $this->assertSame('', $function->getCallableId());
        $this->assertSame(1, $function->getParameters()->count());
        $this->assertEquals([O\Expression::parameter('foo')], $function->getParameters()->getAll());
        $this->assertEquals(['param' => 'scope'], $function->getParameterScopedVariableMap());
        $this->assertSame(__CLASS__, $function->getScopeType());
        $this->assertSame(true, $function->hasScopeType());
        $this->assertSame(4, $function->countBodyExpressions());
        $this->assertSame(2, $function->countBodyExpressionsUntilReturn());
        $this->assertEquals(
                [
                        O\Expression::value('before'),
                        O\Expression::returnExpression(O\Expression::value('return')),
                        O\Expression::value('after'),
                        O\Expression::functionCall(O\Expression::value('boom'))
                ],
                $function->getBodyExpressions()
        );
        $this->assertEquals(
                [
                        O\Expression::value('before'),
                        O\Expression::returnExpression(O\Expression::value('return'))
                ],
                $function->getBodyExpressionsUntilReturn()
        );
    }

    /**
     * @expectedException \Pinq\PinqException
     */
    public function testInternalFunctionThrowsException()
    {
        $function = $this->internalFunction();

        $this->assertTrue($function->isInternal());
        $function->getBodyExpressions();
    }
}
 