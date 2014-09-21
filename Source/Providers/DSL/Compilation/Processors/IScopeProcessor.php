<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Interface of the scope processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeProcessor
{
    /**
     * @return Queries\IScope
     */
    public function buildScope();
}
