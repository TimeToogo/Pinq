<?php

namespace Pinq\Queries\Common\Join;

use Pinq\FunctionExpressionTree;

/**
 * Base class for a join segment.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Base
{
  /**
     * The values to join
     *
     * @var array|\Traversable
     */
    protected $values;

    /**
     * @var boolean
     */
    protected $isGroupJoin;

    /**
     * The join filter.
     *
     * @var IFilter|null
     */
    protected $filter;

    public function __construct($values, $isGroupJoin, IFilter $filter = null)
    {
        $this->values = $values;
        $this->isGroupJoin = $isGroupJoin;
        $this->filter = $filter;
    }

    /**
     * @return array|\Traversable
     */
    final public function getValues()
    {
        return $this->values;
    }

    /**
     * @return boolean
     */
    final public function isGroupJoin()
    {
        return $this->isGroupJoin;
    }

    /**
     * @return boolean
     */
    final public function hasFilter()
    {
        return $this->filter !== null;
    }

    /**
     * @return IFilter|null
     */
    final public function getFilter()
    {
        return $this->filter;
    }
}
