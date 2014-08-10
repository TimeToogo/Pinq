<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Expressions as O;
use Pinq\Queries\Functions;
use Pinq\Queries\Functions\Parameters;

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
 