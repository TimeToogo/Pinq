<?php

namespace Pinq\Queries\Builders\Interpretations;

/**
 * Base class for query expression parsers.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryParser
{
    /**
     * @return string[]
     */
    public function getRequiredParameters();
}