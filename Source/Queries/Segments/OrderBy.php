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

    public function getParameters()
    {
        return call_user_func_array(
                'array_merge',
                array_map(
                        function (Ordering $ordering) {
                            return $ordering->getParameters();
                        },
                        $this->orderings
                )
        );
    }

    public function traverse(ISegmentVisitor $visitor)
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

    /**
     * @param Ordering[] $orderings
     *
     * @return OrderBy
     */
    public function update(array $orderings)
    {
        if ($this->orderings === $orderings) {
            return $this;
        }

        return new self($orderings);
    }
}
