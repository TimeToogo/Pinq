<?php

namespace Pinq\Queries\Operations;

use Pinq\FunctionExpressionTree;
use Pinq\Queries\Segments\Join;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class JoinApplyBase extends Operation
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
     * The function for selecting the resulting values of the join
     *
     * @var FunctionExpressionTree
     */
    protected $applyFunction;

    public function __construct($values, $isGroupJoin, FunctionExpressionTree $applyFunction)
    {
        $this->values = $values;
        $this->isGroupJoin = $isGroupJoin;
        $this->applyFunction = $applyFunction;
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
     * @return FunctionExpressionTree
     */
    final public function getApplyFunctionExpressionTree()
    {
        return $this->applyFunction;
    }
}
