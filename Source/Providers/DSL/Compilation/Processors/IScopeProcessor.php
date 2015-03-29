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
     * @param Queries\IScope $subScope
     *
     * @return IScopeProcessor
     */
    public function forSubScope(Queries\IScope $subScope);

    /**
     * @param Queries\Common\ISource $source
     *
     * @return Queries\Common\ISource
     */
    public function processSource(Queries\Common\ISource $source);

    /**
     * @return Queries\IScope
     */
    public function buildScope();
}
