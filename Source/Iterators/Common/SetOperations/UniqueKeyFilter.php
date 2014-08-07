<?php

namespace Pinq\Iterators\Common\SetOperations;

/**
 * Removes duplicate keys
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UniqueKeyFilter extends UniqueFilter
{
    public function filter($key, $value)
    {
        return $this->set->add($key);
    }
}
