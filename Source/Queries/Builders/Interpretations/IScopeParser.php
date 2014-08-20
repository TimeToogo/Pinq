<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries;

/**
 * Interface of the scope parser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeParser extends IScopeInterpretation, IQueryParser
{
    /**
     * @return Queries\IScope
     */
    public function getScope();
}
