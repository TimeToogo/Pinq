<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\IQueryParameter;
use Pinq\Providers\DSL\Compilation\Parameters\ParameterCollection;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionParameter;
use Pinq\Providers\DSL\Compilation\Parameters\ParameterHasher;
use Pinq\Providers\DSL\Compilation\Parameters\StandardParameter;
use Pinq\Queries\Functions;
use Pinq\Queries\ResolvedParameterRegistry;
use Pinq\Tests\PinqTestCase;

class ParameterCollectionTest extends PinqTestCase
{
    /**
     * @var ParameterCollection
     */
    protected $collection;

    protected function setUp(): void
    {
        $this->collection = new ParameterCollection();
    }

    protected function assertResolvesTo($value, array $resolvedParameters = null)
    {
        $resolvedParameters = $resolvedParameters !== null ?
                new ResolvedParameterRegistry($resolvedParameters) : ResolvedParameterRegistry::none();

        $registry = $this->collection->buildRegistry();
        $this->assertSame(
                $value,
                $registry->resolve($resolvedParameters)->asArray()
        );

        //Test after serialization
        $this->assertSame(
                $value,
                unserialize(serialize($registry))->resolve($resolvedParameters)->asArray()
        );
    }

    public function testCollectionCanEvaluateValueExpressions()
    {
        $this->collection->addExpression(O\Expression::value('val-test'), ParameterHasher::valueType());

        $this->assertResolvesTo(['val-test']);
    }

    public function testCollectionAddsExpressionParameterCorrectly()
    {
        $this->collection->addExpression($expression = O\Expression::value('val-test1'), ParameterHasher::valueType(), null, $instance = new \stdClass());

        $this->assertEquals([new ExpressionParameter($expression, ParameterHasher::valueType(), null, $instance)], $this->collection->getParameters());
    }

    public function testCollectionAddsExpressionWithDataCorrectly()
    {
        $this->collection->addExpression(O\Expression::value('val-test1'), ParameterHasher::valueType(), null, $instance = new \stdClass());

        $this->assertSame($instance, $this->collection->getParameters()[0]->getData());
    }

    public function testParameterCollectionCanEvaluateParameterVariableExpressions()
    {
        $this->collection->addExpression(
                O\Expression::variable(O\Expression::value('var-foo')),
                ParameterHasher::valueType(),
                new Functions\ElementProjection(
                        '',
                        null,
                        null,
                        ['parameter' => 'var-foo'],
                        [],
                        [])
        );

        $this->assertResolvesTo(['param-val'], ['parameter' => 'param-val']);
    }

    public function testParameterCollectionCanEvaluateExpressions()
    {
        $this->collection->addExpression(
                O\Expression::binaryOperation(
                        O\Expression::value('foo'),
                        O\Operators\Binary::CONCATENATION,
                        O\Expression::value('--bar')
                ),
                ParameterHasher::valueType()
        );

        $this->assertResolvesTo(['foo--bar']);
    }

    public function testParameterCollectionCanEvaluateAlteredParameterVariableExpressions()
    {
        $this->collection->addExpression(
                O\Expression::binaryOperation(
                        O\Expression::variable(O\Expression::value('var-foo')),
                        O\Operators\Binary::CONCATENATION,
                        O\Expression::value('--concat')
                ),
                ParameterHasher::valueType(),
                new Functions\ElementProjection(
                        '',
                        null,
                        null,
                        ['param' => 'var-foo'],
                        [],
                        [])
        );

        $this->assertResolvesTo(['abcde--concat'], ['param' => 'abcde']);
        $this->assertResolvesTo(['1--concat'], ['param' => 1]);
        $this->assertResolvesTo(['--concat'], ['param' => null]);
    }

    /**
     * @backupGlobals enabled
     */
    public function testParameterCollectionCanEvaluateSuperGlobalExpression()
    {
        $this->collection->addExpression(
                O\Expression::index(
                        O\Expression::variable(O\Expression::value('_POST')),
                        O\Expression::value('var')
                ),
                ParameterHasher::valueType()
        );

        $_POST['var'] = [1, 2, 3];

        $this->assertResolvesTo([[1, 2, 3]]);
    }

    public function testStandardParameterId()
    {
        $this->collection->addId('foo-bar', ParameterHasher::valueType());

        $this->assertResolvesTo(['123ewq'], ['foo-bar' => '123ewq']);
    }

    public function testCustomParameter()
    {
        $parameterMock = $this->createMock('Pinq\Providers\DSL\Compilation\Parameters\IQueryParameter');
        $parameterMock->expects($this->any())
                ->method('evaluate')
                ->with($this->equalTo(new ResolvedParameterRegistry(['abc' => 'foobar'])))
                ->willReturn('resolved-value');

        $this->collection->add($parameterMock);

        $this->assertResolvesTo(['resolved-value'], ['abc' => 'foobar']);
    }

    public function testContainsParameter()
    {
        $this->collection->addId('foo', ParameterHasher::valueType());
        $this->collection->addId('bar', ParameterHasher::valueType());

        $parameters = $this->collection->getParameters();

        $this->assertTrue($this->collection->contains($parameters[0]));
        $this->assertFalse($this->collection->contains(new StandardParameter('some-id', ParameterHasher::valueType())));
    }

    public function testRemoveParameter()
    {
        $this->collection->addId('foo', ParameterHasher::valueType());
        $this->collection->addId('bar', ParameterHasher::valueType());

        $this->assertCount(2, $this->collection->getParameters());

        $parameters = $this->collection->getParameters();
        $this->collection->remove($parameters[0]);

        $this->assertSame([1 => $parameters[1]], $this->collection->getParameters());

        $this->collection->remove($parameters[1]);

        $this->assertSame([], $this->collection->getParameters());
    }

    public function testCollectionSuppliesCorrectParameterHasToExpressionParameter()
    {
        $this->collection->addExpression(O\Expression::value('val-test'), $hasher = ParameterHasher::valueType());

        $this->assertSame($hasher, $this->collection->getParameters()[0]->getHasher());
    }
}
 