<?php

namespace Pinq\Queries\Segments; 

use \Pinq\FunctionExpressionTree;

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
    protected $Values;
    
    /**
     * @var boolean
     */
    protected $IsGroupJoin;
    
    /**
     * The function for selecting the resulting values of the join
     * 
     * @var FunctionExpressionTree
     */
    protected $JoiningFunction;

    public function __construct($Values, $IsGroupJoin, FunctionExpressionTree $JoiningFunction)
    {
        $this->Values = $Values;
        $this->IsGroupJoin = $IsGroupJoin;
        $this->JoiningFunction = $JoiningFunction;
    }
    
    /**
     * @return array|\Traversable
     */
    final public function GetValues()
    {
        return $this->Values;
    }
    
    /**
     * @return boolean
     */
    final public function IsGroupJoin()
    {
        return $this->IsGroupJoin;
    }
    
    /**
     * @return FunctionExpressionTree
     */
    final public function GetJoiningFunctionExpressionTree()
    {
        return $this->JoiningFunction;
    }
}
