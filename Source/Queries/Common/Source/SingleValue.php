<?php

namespace Pinq\Queries\Common\Source;

/**
 * Single value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SingleValue extends ParameterSourceBase
{
    public function getType()
    {
        return self::SINGLE_VALUE;
    }
}
