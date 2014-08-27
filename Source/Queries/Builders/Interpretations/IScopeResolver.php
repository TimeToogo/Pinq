<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\IQueryable;
use Pinq\Queries;

/**
 * Interface of the scope resolver.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeResolver extends IScopeInterpretation, IQueryResolver
{
    /**
     * @return IQueryable
     */
    public function getQueryable();
}
