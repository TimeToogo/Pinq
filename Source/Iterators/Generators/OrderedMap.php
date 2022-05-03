<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedMap;

/**
 * Implementation of the ordered map iterator using generators for iteration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OrderedMap extends Generator implements IOrderedMap
{
    use Common\OrderedMap;

    public function __construct(\Traversable $iterator = null)
    {
        parent::__construct();

        if ($iterator !== null) {
            $this->setAll($iterator);
        }
    }

    public function setAll(\Traversable $elements)
    {
        $elements = GeneratorScheme::adapter($elements);

        foreach ($elements as $key => &$value) {
            $this->setRef($key, $value);
        }
    }

    public function &getIterator(): \Traversable
    {
        foreach ($this->keys as $position => $key) {
            yield $key => $this->values[$position];
        }
    }
}
