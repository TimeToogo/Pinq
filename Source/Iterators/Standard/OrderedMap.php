<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\IOrderedMap;

/**
 * Implementation of the ordered map using the fetch method for iteration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OrderedMap extends Iterator implements IOrderedMap
{
    use Common\OrderedMap;

    /**
     * @var int
     */
    private $position = 0;

    public function __construct(IIterator $iterator = null)
    {
        parent::__construct();

        if ($iterator !== null) {
            $this->setAll($iterator);
        }
    }

    public function setAll(\Traversable $elements)
    {
        $elements = IteratorScheme::adapter($elements);

        $elements->rewind();
        while ($element = $elements->fetch()) {
            $this->setRef($element[0], $element[1]);
        }
    }

    protected function doRewind()
    {
        $this->position = 0;
    }

    protected function doFetch()
    {
        $position =& $this->position;
        if (isset($this->keys[$position]) || array_key_exists($position, $this->keys)) {
            $element = [$this->keys[$position], &$this->values[$position]];
            $position++;

            return $element;
        }
    }
}
