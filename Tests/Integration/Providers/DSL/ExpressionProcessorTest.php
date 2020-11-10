<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Processors\Expression\ExpressionProcessor;
use Pinq\Queries\Functions;
use Pinq\Tests\PinqTestCase;

class ExpressionProcessorTest extends PinqTestCase
{
    public function testWalksBodyAndParameterExpressions()
    {
        $processor = $this->createPartialMock('Pinq\Providers\DSL\Compilation\Processors\Expression\ExpressionProcessor', ['walkValue']);
        $processor->expects($this->exactly(2))
                    ->method('walkValue')
                    ->will($this->returnValue(O\Expression::value('updated')));

        /** @var ExpressionProcessor $processor */

        $function = new Functions\ElementProjection(
                '',
                null,
                null,
                [],
                [O\Expression::parameter('param', 'abc', O\Expression::value('default'))],
                [O\Expression::value('body')]
        );

        $processedFunction = $processor->processFunction($function);

        $this->assertEquals([O\Expression::value('updated')], $processedFunction->getBodyExpressions());
        $this->assertEquals(O\Expression::parameter('param', 'abc', O\Expression::value('updated')), $processedFunction->getParameters()->getValue());
    }
}
 