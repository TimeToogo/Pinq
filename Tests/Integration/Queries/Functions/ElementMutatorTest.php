<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Queries\Functions;

class ElementMutatorTest extends MutatorBaseTest
{
    /**
     * @return callable
     */
    protected function functionFactory()
    {
        return Functions\ElementMutator::factory();
    }
}
