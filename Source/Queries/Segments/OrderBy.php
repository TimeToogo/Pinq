<?php

namespace Pinq\Queries\Segments; 

use \Pinq\FunctionExpressionTree;

class OrderBy extends Segment
{
    /**
     * @var FunctionExpressionTree[]
     */
    private $FunctionExpressionTrees;

    /**
     * @var bool[]
     */
    private $IsAscendingArray;

    public function __construct(array $FunctionExpressionTrees, array $IsAscendingArray)
    {
        if (array_keys($FunctionExpressionTrees) !== array_keys($IsAscendingArray)) {
            throw new \Pinq\PinqException('Cannot construct order by: expression tree array and is asceding array keys do not match');
        }

        $this->FunctionExpressionTrees = $FunctionExpressionTrees;
        $this->IsAscendingArray = $IsAscendingArray;
    }

    public function GetType()
    {
        return self::OrderBy;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkOrderBy($this);
    }

    /**
     * @return FunctionExpressionTree[]
     */
    public function GetFunctionExpressionTrees()
    {
        return $this->FunctionExpressionTrees;
    }

    /**
     * @return bool[]
     */
    public function GetIsAscendingArray()
    {
        return $this->IsAscendingArray;
    }
    
    /**
     * @param boolean $IsAscending
     */
    public function ThenBy(FunctionExpressionTree $FunctionExpressionTree, $IsAscending) 
    {
        return new self(
                array_merge($this->FunctionExpressionTrees, [$FunctionExpressionTree]),
                array_merge($this->IsAscendingArray, [$IsAscending]));
    }
    
    public function Update(array $FunctionExpressionTrees, array $IsAscendingArray)
    {
        if($this->FunctionExpressionTrees === $FunctionExpressionTrees
                && $this->IsAscendingArray === $IsAscendingArray) {
            return $this;
        }
        
        return new self($FunctionExpressionTrees, $IsAscendingArray);
    }
}
