<?php 

namespace Pinq\Queries\Requests;

use Pinq\FunctionExpressionTree;

/**
 * Base class for a request which optionally projects the values with
 * the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ProjectionRequest extends Request
{
    /**
     * @var FunctionExpressionTree|null
     */
    private $functionExpressionTree;
    
    public function __construct(FunctionExpressionTree $functionExpressionTree = null)
    {
        $this->functionExpressionTree = $functionExpressionTree;
    }
    
    /**
     * @return boolean
     */
    public function hasFunctionExpressionTree()
    {
        return $this->functionExpressionTree !== null;
    }
    
    /**
     * @return FunctionExpressionTree|null
     */
    public function getFunctionExpressionTree()
    {
        return $this->functionExpressionTree;
    }
}