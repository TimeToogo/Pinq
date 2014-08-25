<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;

class ElementProjectionTest extends ProjectionBaseTest
{
    /**
     * @return callable
     */
    protected function functionFactory()
    {
        return Functions\ElementProjection::factory();
    }

    public function testFullParameters()
    {
        /** @var $function Functions\ElementProjection */
        $function = $this->buildFunction(
                '',
                null,
                null,
                [],
                [O\Expression::parameter('value'), O\Expression::parameter('key')]
        );

        $this->assertSame(true, $function->getParameters()->hasValue());
        $this->assertSame('value', $function->getParameters()->getValue()->getName());
        $this->assertSame(true, $function->getParameters()->hasKey());
        $this->assertSame('key', $function->getParameters()->getKey()->getName());
        $this->assertSame(false, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getUnused());
        $this->assertSame([], $function->getParameters()->getUnusedParameterDefaultMap());
    }

    public function testEmptyParameters()
    {
        /** @var $function Functions\ElementProjection */
        $function = $this->emptyFunction();

        $this->assertSame(false, $function->getParameters()->hasValue());
        $this->assertSame(null, $function->getParameters()->getValue());
        $this->assertSame(false, $function->getParameters()->hasKey());
        $this->assertSame(null, $function->getParameters()->getKey());
        $this->assertSame(false, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getUnused());
        $this->assertSame([], $function->getParameters()->getUnusedParameterDefaultMap());
    }

    public function testRequiredExcessiveParameters()
    {
        /** @var $function Functions\ElementProjection */
        $function = $this->buildFunction(
                '',
                null,
                null,
                [],
                [
                        O\Expression::parameter('value'),
                        O\Expression::parameter('key'),
                        O\Expression::parameter('excessive1'),
                        O\Expression::parameter('excessive2')
                ]
        );

        $this->assertSame(true, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertEquals(
                [O\Expression::parameter('excessive1'), O\Expression::parameter('excessive2')],
                $function->getParameters()->getRequiredUnusedParameters()
        );
        $this->assertEquals(
                [O\Expression::parameter('excessive1'), O\Expression::parameter('excessive2')],
                $function->getParameters()->getUnused()
        );
        $this->assertEquals(
                ['excessive1' => null, 'excessive2' => null],
                $function->getParameters()->getUnusedParameterDefaultMap()
        );
        $this->assertEquals(
                [],
                $function->getEvaluationContextFactory()->getEvaluationContext()->getVariableTable()
        );
    }

    public function testExcessiveParametersWithDefault()
    {
        /** @var $function Functions\ElementProjection */
        $function = $this->buildFunction(
                '',
                null,
                null,
                [],
                [
                        O\Expression::parameter('value'),
                        O\Expression::parameter('key'),
                        O\Expression::parameter('excessive1', null, O\Expression::value([true])),
                        O\Expression::parameter('excessive2', null, O\Expression::value([false]))
                ]
        );

        $this->assertSame(false, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertEquals([], $function->getParameters()->getRequiredUnusedParameters());
        $this->assertEquals(
                [
                        O\Expression::parameter('excessive1', null, O\Expression::value([true])),
                        O\Expression::parameter('excessive2', null, O\Expression::value([false]))
                ],
                $function->getParameters()->getUnused()
        );
        $this->assertEquals(
                ['excessive1' => O\Expression::value([true]), 'excessive2' => O\Expression::value([false])],
                $function->getParameters()->getUnusedParameterDefaultMap()
        );
        $this->assertEquals(
                ['excessive1' => [true], 'excessive2' => [false]],
                $function->getEvaluationContextFactory()->getEvaluationContext()->getVariableTable()
        );
    }

    public function testFunctionWithDefaultRelativeConstant()
    {
        /** @var $function Functions\ElementProjection */
        $function = $this->buildFunction(
                '',
                null,
                __NAMESPACE__,
                [],
                [
                        O\Expression::parameter('value'),
                        O\Expression::parameter('key'),
                        O\Expression::parameter('excessive1', null, O\Expression::constant('__RELATIVE_CONSTANT')),
                        O\Expression::parameter('excessive2', null, O\Expression::value([false]))
                ]
        );

        $this->assertSame(false, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertEquals([], $function->getParameters()->getRequiredUnusedParameters());
        $this->assertEquals(
                [
                        O\Expression::parameter('excessive1', null, O\Expression::constant('__RELATIVE_CONSTANT')),
                        O\Expression::parameter('excessive2', null, O\Expression::value([false]))
                ],
                $function->getParameters()->getUnused()
        );

        $this->assertEquals(
                [
                        'excessive1' => O\Expression::constant('__RELATIVE_CONSTANT'),
                        'excessive2' => O\Expression::value([false])
                ],
                $function->getParameters()->getUnusedParameterDefaultMap()
        );

        //This fails in PHP due to strange behaviour with closures and relative constants.
        //Bug reported: https://bugs.php.net/bug.php?id=67897
        if (defined('HHVM_VERSION')) {
            define('__RELATIVE_CONSTANT', 'in global namespace');
            $this->assertEquals(
                    ['excessive1' => 'in global namespace', 'excessive2' => [false]],
                    $function->getEvaluationContextFactory()->getEvaluationContext()->getVariableTable()
            );

            //Should take precedence
            define(__NAMESPACE__ . '\\__RELATIVE_CONSTANT', 'in relative namespace');
            $this->assertEquals(
                    ['excessive1' => 'in relative namespace', 'excessive2' => [false]],
                    $function->getEvaluationContextFactory()->getEvaluationContext()->getVariableTable()
            );
        }
    }
}
