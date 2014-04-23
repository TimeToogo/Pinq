<?php 

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

/**
 * Query segment for grouping the values base on the supplied 
 * grouping functions.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupBy extends Segment
{
    /**
     * @var FunctionExpressionTree[]
     */
    private $functionExpressionTrees;
    
    public function __construct(array $functionExpressionTrees)
    {
        $this->functionExpressionTrees = $functionExpressionTrees;
    }
    
    public function getType()
    {
        return self::GROUP_BY;
    }
    
    public function getFunctionExpressionTrees()
    {
        return $this->functionExpressionTrees;
    }
    
    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkGroupBy($this);
    }
    
    public function andBy(FunctionExpressionTree $functionExpressionTree)
    {
        return new self(array_merge($this->functionExpressionTrees, [$functionExpressionTree]));
    }
    
    public function update(array $functionExpressionTrees)
    {
        if ($this->functionExpressionTrees === $functionExpressionTrees) {
            return $this;
        }
        
        return new self($functionExpressionTrees);
    }
}