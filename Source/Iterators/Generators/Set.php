<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\ISet;

/**
 * Implementation of the set using generators for iteration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Set extends Generator implements ISet
{
    use Common\Set;

    public function __construct(IGenerator $values = null)
    {
        parent::__construct();

        if ($values !== null) {
            foreach ($values as &$value) {
                $this->addRef($value);
            }
        }
    }

    public function &getIterator(): \Traversable
    {
        foreach ($this->values as &$value) {
            yield $value;
        }
    }
}
