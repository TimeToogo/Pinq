<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

/**
 * Implementation of the parameter hasher that returns a
 * unique hash based on the *value* of a variable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ValueTypeHasher implements IParameterHasher
{
    public function hash($value)
    {
        return md5(serialize($value));
    }
}
