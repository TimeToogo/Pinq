<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\Functions;

/**
 * Query segment for ordering the values with the supplied functions
 * and order directions
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OrderBy extends Segment
{
    /**
     * @var Ordering[]
     */
    private $orderings;

    public function __construct(array $orderings)
    {
        $this->orderings = $orderings;
    }

    public function getType()
    {
        return self::ORDER_BY;
    }

    public function traverse(SegmentVisitor $visitor)
    {
        return $visitor->visitOrderBy($this);
    }

    /**
     * @return Ordering[]
     */
    public function getOrderings()
    {
        return $this->orderings;
    }

    public function update(array $orderings)
    {
        if ($this->orderings === $orderings) {
            return $this;
        }

        return new self($orderings);
    }
}
