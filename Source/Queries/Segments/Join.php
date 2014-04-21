<?php

namespace Pinq\Queries\Segments; 

use \Pinq\FunctionExpressionTree;

/**
 * Query segment for a join filtered by the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Join extends JoinBase
{
    /**
     * The join filter expression tree
     *
     * @var FunctionExpressionTree 
     */
    private $OnFunction;

    public function __construct($Values, $IsGroupJoin, FunctionExpressionTree $OnFunction, FunctionExpressionTree $JoiningFunction)
    {
        parent::__construct($Values, $IsGroupJoin, $JoiningFunction);
        $this->OnFunction = $OnFunction;
    }

    public function GetType()
    {
        return self::Join;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkJoin($this);
    }

    /**
     * @return FunctionExpressionTree
     */
    public function GetOnFunctionExpressionTree()
    {
        return $this->OnFunction;
    }
    
    public function Update($Values, $IsGroupJoin, FunctionExpressionTree $OnFunction, FunctionExpressionTree $JoiningFunction)
    {
        if($this->Values === $Values
                && $this->IsGroupJoin === $IsGroupJoin
                && $this->OnFunction === $OnFunction
                && $this->JoiningFunction = $JoiningFunction) {
            return $this;
        }
        
        return new self($Values, $IsGroupJoin, $OnFunction, $JoiningFunction);
    }
}
