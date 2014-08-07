<?php

namespace Pinq\Queries\Common\Join;

/**
 * Interface for a join filter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFilter
{
    const CUSTOM   = 0;
    const EQUALITY = 1;

    /**
     * @return int
     */
    public function getType();
}
