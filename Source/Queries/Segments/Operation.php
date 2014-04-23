<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for a set/range operation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Operation extends Segment
{
    const UNION = 1;
    const INTERSECT = 2;
    const DIFFERENCE = 3;
    const APPEND = 4;
    const WHERE_IN = 5;
    const EXCEPT = 6;

    /**
     * @var int
     */
    private $operationType;

    /**
     * @var \Traversable|array
     */
    private $values;

    public function __construct($operationType, $values)
    {
        if (!\Pinq\Utilities::isIterable($values)) {
            throw \Pinq\PinqException::invalidIterable(__METHOD__, $values);
        }

        if (!self::isValid($operationType)) {
            throw new \Pinq\PinqException('Invalid operation type');
        }

        $this->operationType = $operationType;
        $this->values = $values;
    }

    final public static function isValid($operationType)
    {
        return in_array($operationType, [
            self::UNION,
            self::INTERSECT,
            self::DIFFERENCE,
            self::APPEND,
            self::WHERE_IN,
            self::EXCEPT
        ]);
    }

    public function getType()
    {
        return self::OPERATE;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkOperation($this);
    }

    /**
     * @return int
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     * @return \Traversable|array
     */
    public function getTraversable()
    {
        return $this->values;
    }
}
