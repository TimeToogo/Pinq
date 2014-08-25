<?php

namespace Pinq\Queries\Common\Join;

use Pinq\Expressions as O;

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

    /**
     * @return string[]
     */
    public function getParameters();

    /**
     * @param O\ExpressionWalker $walker
     *
     * @return static
     */
    public function walk(O\ExpressionWalker $walker);
}
