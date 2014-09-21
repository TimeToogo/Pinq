<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\Common;
use Pinq\Queries\Functions;
use Pinq\Queries\ISegment;

/**
 * Query segment for joining values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Join implements ISegment
{
    /**
     * The join options.
     *
     * @var Common\Join\Options
     */
    protected $options;

    /**
     * The function for selecting the resulting values of the join
     *
     * @var Functions\ConnectorProjection
     */
    protected $joiningFunction;

    public function __construct(
            Common\Join\Options $options,
            Functions\ConnectorProjection $joiningFunction
    ) {
        $this->options         = $options;
        $this->joiningFunction = $joiningFunction;
    }

    public function getType()
    {
        return self::JOIN;
    }

    public function getParameters()
    {
        return array_merge($this->options->getParameters(), $this->joiningFunction->getParameterIds());
    }

    /**
     * @return Common\Join\Options
     */
    final public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Functions\ConnectorProjection
     */
    final public function getJoiningFunction()
    {
        return $this->joiningFunction;
    }

    public function traverse(ISegmentVisitor $visitor)
    {
        return $visitor->visitJoin($this);
    }

    /**
     * @param Common\Join\Options           $options
     * @param Functions\ConnectorProjection $joiningFunction
     *
     * @return Join
     */
    public function update(
            Common\Join\Options $options,
            Functions\ConnectorProjection $joiningFunction
    ) {
        if ($this->options === $options && $joiningFunction === $this->joiningFunction) {
            return $this;
        }

        return new self($options, $joiningFunction);
    }
}
