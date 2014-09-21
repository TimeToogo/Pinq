<?php

namespace Pinq\Queries\Segments;

use Pinq\PinqException;
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
            throw new PinqException('Invalid operation type');
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

    public function getParameters()
    {
        return $this->source->getParameters();
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

    /**
     * @param int            $operationType
     * @param Common\ISource $source
     *
     * @return Operation
     */
    public function update($operationType, Common\ISource $source)
    {
        if ($this->operationType === $operationType && $this->source === $source) {
            return $this;
        }

        return new self($operationType, $source);
    }

    /**
     * @param Common\ISource $source
     *
     * @return Operation
     */
    public function updateSource(Common\ISource $source)
    {
        return $this->update($this->operationType, $source);
    }
}
