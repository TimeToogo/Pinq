<?php

namespace Pinq\Queries\Segments;

use Pinq\Queries\Common\Join as CommonJoin;
use Pinq\FunctionExpressionTree;

/**
 * Base class for a join query segment with the joined values and the
 * resulting value function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Join extends CommonJoin\Base implements \Pinq\Queries\ISegment
{
    /**
     * The function for selecting the resulting values of the join
     *
     * @var FunctionExpressionTree
     */
    protected $joiningFunction;

    public function __construct(
            $values, 
            $isGroupJoin, 
            CommonJoin\IFilter $filter = null, 
            FunctionExpressionTree $joiningFunction,
            $hasDefault = false, 
            $defaultValue = null, 
            $defaultKey = null)
    {
        parent::__construct($values, $isGroupJoin, $filter, $hasDefault, $defaultValue, $defaultKey);
        
        $this->joiningFunction = $joiningFunction;
    }

    public function getType()
    {
        return self::JOIN;
    }

    /**
     * @return FunctionExpressionTree
     */
    final public function getJoiningFunctionExpressionTree()
    {
        return $this->joiningFunction;
    }
    
    public function traverse(SegmentWalker $walker)
    {
        $walker->walkJoin($this);
    }

}
