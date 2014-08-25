<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;

class ConnectorProjectionTest extends ProjectionBaseTest
{
    /**
     * @return callable
     */
    protected function functionFactory()
    {
        return Functions\ConnectorProjection::factory();
    }

    public function testFullParameters()
    {
        /** @var $function Functions\ConnectorProjection */
        $function = $this->buildFunction(
                '',
                null,
                null,
                [],
                [
                        O\Expression::parameter('outer-value'),
                        O\Expression::parameter('inner-value'),
                        O\Expression::parameter('outer-key'),
                        O\Expression::parameter('inner-key')
                ]
        );

        $this->assertSame(true, $function->getParameters()->hasOuterKey());
        $this->assertSame('outer-value', $function->getParameters()->getOuterValue()->getName());
        $this->assertSame(true, $function->getParameters()->hasInnerValue());
        $this->assertSame('inner-value', $function->getParameters()->getInnerValue()->getName());
        $this->assertSame(true, $function->getParameters()->hasOuterKey());
        $this->assertSame('outer-key', $function->getParameters()->getOuterKey()->getName());
        $this->assertSame(true, $function->getParameters()->hasInnerKey());
        $this->assertSame('inner-key', $function->getParameters()->getInnerKey()->getName());
        $this->assertSame(false, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getUnused());
        $this->assertSame([], $function->getParameters()->getUnusedParameterDefaultMap());
    }
}
