<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * Removes duplicate values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UniqueFilter extends SetFilter
{
    public function initialize()
    {
        $this->set = $this->scheme->createSet();
    }

    public function filter($key, $value)
    {
        return $this->set->add($value);
    }
}
