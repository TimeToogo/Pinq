<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Interface containing the structural information of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionMagic
{
    /**
     * Gets the resolved magic constants.
     *
     * @return IMagicConstants
     */
    public function getConstants();

    /**
     * Gets the resolved magic constants.
     *
     * @return IMagicScopes
     */
    public function getScopes();
}
