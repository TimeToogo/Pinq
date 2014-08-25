<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionCollection;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionParameter;
use Pinq\Queries\Functions;
use Pinq\Queries\ResolvedParameterRegistry;
use Pinq\Tests\PinqTestCase;

class ExpressionParameterCollectionTest extends PinqTestCase
{
    /**
     * @var ExpressionCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new ExpressionCollection();
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
        $this->collection->add(O\Expression::value('val-test'));

        $this->assertResolvesTo(['val-test']);
    }

    public function testCollectionAddsExpressionCorrectly()
    {
        $this->collection->add($expression = O\Expression::value('val-test1'));

        $this->assertContains($expression, $this->collection->getExpressions());
        $this->assertSame('val-test1', $this->collection->buildRegistry()->resolve(ResolvedParameterRegistry::none())->evaluate($expression));
    }

    public function testCollectionAddsExpressionParameterCorrectly()
    {
        $this->collection->add($expression = O\Expression::value('val-test1'), null, $instance = new \stdClass());

        $this->assertEquals([new ExpressionParameter($expression, null, $instance)], $this->collection->getExpressionParameters());
    }

    public function testCollectionAddsExpressionWithDataCorrectly()
    {
        $this->collection->add($expression = O\Expression::value('val-test1'), null, $instance = new \stdClass());

        $this->assertSame($instance, $this->collection->getData($expression));
    }

    public function testParameterCollectionCanEvaluateParameterVariableExpressions()
    {
        $this->collection->add(
                O\Expression::variable(O\Expression::value('var-foo')),
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
        $this->collection->add(
                O\Expression::binaryOperation(
                        O\Expression::value('foo'),
                        O\Operators\Binary::CONCATENATION,
                        O\Expression::value('--bar')
                )
        );

        $this->assertResolvesTo(['foo--bar']);
    }

    public function testParameterCollectionCanEvaluateAlteredParameterVariableExpressions()
    {
        $this->collection->add(
                O\Expression::binaryOperation(
                        O\Expression::variable(O\Expression::value('var-foo')),
                        O\Operators\Binary::CONCATENATION,
                        O\Expression::value('--concat')
                ),
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
        $this->collection->add(
                O\Expression::index(
                        O\Expression::variable(O\Expression::value('_POST')),
                        O\Expression::value('var')
                )
        );

        $_POST['var'] = [1, 2, 3];

        $this->assertResolvesTo([[1, 2, 3]]);
    }

    public function testParameterCollectionForFunctionContextVariables()
    {
        $context = $this->collection
                ->forFunction(
                        new Functions\ElementProjection(
                                '',
                                null,
                                null,
                                ['param1' => 'var1', 'param2' => 'var2'],
                                [],
                                [])
                );

        $context->add(O\Expression::variable(O\Expression::value('var1')));
        $context->add(O\Expression::variable(O\Expression::value('var2')));
        $context->add(O\Expression::binaryOperation(
                O\Expression::variable(O\Expression::value('var1')),
                O\Operators\Binary::CONCATENATION,
                O\Expression::variable(O\Expression::value('var2'))
        ));

        $this->assertResolvesTo([5, 'abcde', '5abcde'], ['param1' => 5, 'param2' => 'abcde']);
    }
}
 