<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;

class AggregatorTest extends FunctionTest
{
    /**
     * @return callable
     */
    protected function functionFactory()
    {
        return Functions\Aggregator::factory();
    }

    public function testFullParameters()
    {
        /** @var $function Functions\Aggregator */
        $function = $this->buildFunction(
                '',
                null,
                null,
                [],
                [O\Expression::parameter('aggregate'), O\Expression::parameter('value')]
        );

        $this->assertSame(true, $function->getParameters()->hasAggregateValue());
        $this->assertSame('aggregate', $function->getParameters()->getAggregateValue()->getName());
        $this->assertSame(true, $function->getParameters()->hasValue());
        $this->assertSame('value', $function->getParameters()->getValue()->getName());
        $this->assertSame(false, $function->getParameters()->hasRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getRequiredUnusedParameters());
        $this->assertSame([], $function->getParameters()->getUnused());
        $this->assertSame([], $function->getParameters()->getUnusedParameterDefaultMap());
    }
}
