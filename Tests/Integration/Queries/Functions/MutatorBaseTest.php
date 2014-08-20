<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;

abstract class MutatorBaseTest extends FunctionTest
{
    protected function functionWithFirstParameterReference()
    {
        return $this->buildFunction(
                '',
                __CLASS__,
                __NAMESPACE__,
                [],
                [O\Expression::parameter('true', null, null, true)],
                []
        );
    }

    public function testFunctionThatHasFirstParameterByRef()
    {
        /** @var $function Functions\MutatorBase */
        $function = $this->functionWithFirstParameterReference();

        $this->assertSame(true, $function->valueParameterIsReference());
    }

    public function testFunctionWithoutFirstParameterByRef()
    {
        /** @var $function Functions\MutatorBase */
        $function = $this->functionWithReturnStatement();

        $this->assertSame(false, $function->valueParameterIsReference());
    }
}
