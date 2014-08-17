<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries;
use Pinq\Queries\Common;

/**
 * Query segment for a set/multiset operation
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Operation extends Segment
{
    const UNION      = 1;
    const INTERSECT  = 2;
    const DIFFERENCE = 3;
    const APPEND     = 4;
    const WHERE_IN   = 5;
    const EXCEPT     = 6;

    /**
     * @var int
     */
    private $operationType;

    /**
     * @var Common\ISource
     */
    private $source;

    public function __construct($operationType, Common\ISource $source)
    {
        if (!self::isValid($operationType)) {
            throw new \Pinq\PinqException('Invalid operation type');
        }

        $this->operationType = $operationType;
        $this->source        = $source;
    }

    final public static function isValid($operationType)
    {
        return in_array(
                $operationType,
                [
                        self::UNION,
                        self::INTERSECT,
                        self::DIFFERENCE,
                        self::APPEND,
                        self::WHERE_IN,
                        self::EXCEPT
                ],
                true
        );
    }

    public function getType()
    {
        return self::OPERATION;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitOperation($this);
    }

    /**
     * @return int
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     * @return Common\ISource
     */
    public function getSource()
    {
        return $this->source;
    }

    public function update($operationType, Common\ISource $source)
    {
        if ($this->operationType === $operationType && $this->source === $source) {
            return $this;
        }

        return new self($operationType, $source);
    }
}
