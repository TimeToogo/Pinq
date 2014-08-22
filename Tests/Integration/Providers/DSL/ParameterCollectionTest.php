<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\ParameterCollection;
use Pinq\Queries\Functions;
use Pinq\Queries\ResolvedParameterRegistry;
use Pinq\Tests\PinqTestCase;

class ParameterCollectionTest extends PinqTestCase
{
    /**
     * @var ParameterCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new ParameterCollection();
    }

    protected function assertResolvesTo($value, array $resolvedParameters = null)
    {
        $resolvedParameters = $resolvedParameters !== null ? new ResolvedParameterRegistry($resolvedParameters) : ResolvedParameterRegistry::none(
        );

        $this->assertSame(
                $value,
                $this->collection->resolveParameters($resolvedParameters)
        );

        //Test after serialization
        $this->assertSame(
                $value,
                unserialize(serialize($this->collection))->resolveParameters($resolvedParameters)
        );
    }

    public function testParameterCollectionCanEvaluateValueExpressions()
    {
        $this->collection->addExpression('test', O\Expression::value('val-test'));

        $this->assertResolvesTo(['test' => 'val-test']);
    }

    public function testParameterCollectionCanEvaluateParameterVariableExpressions()
    {
        $this->collection->addExpression(
                'test1',
                O\Expression::variable(O\Expression::value('var-foo')),
                new Functions\ElementProjection(
                        '',
                        null,
                        null,
                        ['parameter' => 'var-foo'],
                        [],
                        [])
        );

        $this->assertResolvesTo(['test1' => 'param-val'], ['parameter' => 'param-val']);
    }

    public function testParameterCollectionCanEvaluateExpressions()
    {
        $this->collection->addExpression(
                'val',
                O\Expression::binaryOperation(
                        O\Expression::value('foo'),
                        O\Operators\Binary::CONCATENATION,
                        O\Expression::value('--bar')
                )
        );

        $this->assertResolvesTo(['val' => 'foo--bar']);
    }

    public function testParameterCollectionCanEvaluateAlteredParameterVariableExpressions()
    {
        $this->collection->addExpression(
                'value',
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

        $this->assertResolvesTo(['value' => 'abcde--concat'], ['param' => 'abcde']);
        $this->assertResolvesTo(['value' => '1--concat'], ['param' => 1]);
        $this->assertResolvesTo(['value' => '--concat'], ['param' => null]);
    }

    /**
     * @backupGlobals enabled
     */
    public function testParameterCollectionCanEvaluateSuperGlobalExpression()
    {
        $this->collection->addExpression(
                'sup',
                O\Expression::index(
                        O\Expression::variable(O\Expression::value('_POST')),
                        O\Expression::value('var')
                )
        );

        $_POST['var'] = [1, 2, 3];

        $this->assertResolvesTo(['sup' => [1, 2, 3]]);
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

        $context->addExpression('val1', O\Expression::variable(O\Expression::value('var1')));
        $context->addExpression('val2', O\Expression::variable(O\Expression::value('var2')));
        $context->addExpression('val-combine', O\Expression::binaryOperation(
                O\Expression::variable(O\Expression::value('var1')),
                O\Operators\Binary::CONCATENATION,
                O\Expression::variable(O\Expression::value('var2'))
        ));

        $this->assertResolvesTo(['val1' => 5, 'val2' => 'abcde', 'val-combine' => '5abcde'], ['param1' => 5, 'param2' => 'abcde']);
    }
}
