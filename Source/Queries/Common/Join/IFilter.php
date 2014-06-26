<?php

namespace Pinq\Queries\Common\Join;

/**
 * Interface for a join filter.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IFilter
{
    const ON = 0;
    const EQUALITY = 1;

    /**
     * @return int
     */
    public function getType();
}
