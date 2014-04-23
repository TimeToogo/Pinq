<?php 

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

/**
 * Base class for a join query segment with the joined values and the 
 * resulting value function 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class JoinBase extends Segment
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
    protected $joiningFunction;
    
    public function __construct($values, $isGroupJoin, FunctionExpressionTree $joiningFunction)
    {
        $this->values = $values;
        $this->isGroupJoin = $isGroupJoin;
        $this->joiningFunction = $joiningFunction;
    }
    
    /**
     * @return array|\Traversable
     */
    public final function getValues()
    {
        return $this->values;
    }
    
    /**
     * @return boolean
     */
    public final function isGroupJoin()
    {
        return $this->isGroupJoin;
    }
    
    /**
     * @return FunctionExpressionTree
     */
    public final function getJoiningFunctionExpressionTree()
    {
        return $this->joiningFunction;
    }
}