<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\PinqException;
use Pinq\Queries\Functions;
use Pinq\Queries\Functions\IFunction;
use Pinq\Queries\ResolvedParameterRegistry;
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
     * @param string $namespace
     * @param array  $parameterScopedVariableMap
     * @param array  $parameterExpressions
     * @param array  $bodyExpressions
     *
     * @return IFunction
     */
    protected function buildFunction(
            $callableParameter,
            $scopeType = null,
            $namespace = null,
            array $parameterScopedVariableMap = [],
            array $parameterExpressions = [],
            array $bodyExpressions = null
    ) {
        $factory = $this->functionFactory();

        return $factory(
                $callableParameter,
                $scopeType,
                $namespace,
                $parameterScopedVariableMap,
                $parameterExpressions,
                $bodyExpressions
        );
    }

    protected function emptyFunction()
    {
        return $this->buildFunction('CALLABLE!!', null, null, [], [], []);
    }

    protected function internalFunction()
    {
        return $this->buildFunction('CALLABLE!!', null, null, [], [O\Expression::parameter('str')], null);
    }

    protected function functionWithReturnStatement()
    {
        return $this->buildFunction(
                '',
                __CLASS__,
                __NAMESPACE__,
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
        $this->assertSame(false, $function->hasScopeType());
        $this->assertSame(null, $function->getScopeType());
        $this->assertSame(false, $function->hasNamespace());
        $this->assertSame(null, $function->getNamespace());
        $this->assertSame(0, $function->countBodyExpressions());
        $this->assertSame(0, $function->countBodyExpressionsUntilReturn());
        $this->assertEquals([], $function->getBodyExpressions());
        $this->assertEquals([], $function->getBodyExpressionsUntilReturn());
        $this->assertEquals(['CALLABLE!!'], $function->getParameterIds());
        $this->assertEquals(
                O\EvaluationContext::globalScope(),
                $function->getEvaluationContextFactory()->getEvaluationContext(ResolvedParameterRegistry::none())
        );
    }

    public function testFunctionWithReturnStatement()
    {
        $function = $this->functionWithReturnStatement();

        $this->assertSame('', $function->getCallableId());
        $this->assertSame(1, $function->getParameters()->count());
        $this->assertEquals([O\Expression::parameter('foo')], $function->getParameters()->getAll());
        $this->assertEquals(['param' => 'scope'], $function->getParameterScopedVariableMap());
        $this->assertEquals(['', 'param'], $function->getParameterIds());
        $this->assertSame(true, $function->hasScopeType());
        $this->assertSame(__CLASS__, $function->getScopeType());
        $this->assertSame(true, $function->hasNamespace());
        $this->assertSame(__NAMESPACE__, $function->getNamespace());
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
        $this->assertEquals(
                new O\EvaluationContext(__NAMESPACE__, __CLASS__, null, ['scope' => 'value']),
                $function->getEvaluationContextFactory()->getEvaluationContext(new ResolvedParameterRegistry(['param' => 'value']))
        );
    }

    public function testInternalFunctionThrowsException()
    {
        $this->expectException(PinqException::class);
        $function = $this->internalFunction();

        $this->assertTrue($function->isInternal());
        $function->getBodyExpressions();
    }

    public function testSerialization()
    {
        $function1 = $this->internalFunction();
        $this->assertEquals($function1, unserialize(serialize($function1)));

        $function2 = $this->emptyFunction();
        $this->assertEquals($function2, unserialize(serialize($function2)));

        $function3 = $this->functionWithReturnStatement();

        $this->assertEquals($function3, unserialize(serialize($function3)));
    }

    public function testUpdate()
    {
        $function = $this->functionWithReturnStatement();

        $this->assertEquals(
                $function,
                $function->update(
                        $function->getScopeType(),
                        $function->getNamespace(),
                        $function->getParameterScopedVariableMap(),
                        $function->getParameters()->getAll(),
                        $function->getBodyExpressions()
                )
        );
    }

    public function testUpdateBody()
    {
        $function = $this->functionWithReturnStatement();

        $this->assertEquals(
                $function,
                $function->updateBody($function->getBodyExpressions())
        );
    }

    public function testWalk()
    {
        $function = $this->functionWithReturnStatement();

        $this->assertEquals(
                $function,
                $function->walk(new O\DynamicExpressionWalker([]))
        );
    }
}
