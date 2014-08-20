<?php

namespace Pinq\Tests\Integration\Queries\Functions;

use Pinq\Queries\Functions;

class ConnectorMutatorTest extends MutatorBaseTest
{
    /**
     * @return callable
     */
    protected function functionFactory()
    {
        return Functions\ConnectorMutator::factory();
    }
}
