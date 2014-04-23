<?php 

namespace Pinq\Queries\Requests;

use Pinq\FunctionExpressionTree;

/**
 * Request query for a custom aggregate using the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Aggregate extends Request
{
    /**
     * @var FunctionExpressionTree
     */
    private $functionExpressionTree;
    
    public function __construct(FunctionExpressionTree $functionExpressionTree)
    {
        $this->functionExpressionTree = $functionExpressionTree;
    }
    
    public function getType()
    {
        return self::AGGREGATE;
    }
    
    /**
     * @return FunctionExpressionTree
     */
    public function getFunctionExpressionTree()
    {
        return $this->functionExpressionTree;
    }
    
    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitAggregate($this);
    }
}