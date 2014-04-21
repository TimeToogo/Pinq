<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

/**
 * The API for a cache implementation to store the expression trees
 * of parsed functions.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IFunctionCache
{
    /**
     * Save the supplied expression tree of the functiom to the cache
     * 
     * @param string $FunctionHash
     * @param FunctionExpressionTree $FunctionExpressionTree
     * @return void
     */
    public function Save(
            $FunctionHash,
            FunctionExpressionTree $FunctionExpressionTree);
    
    /**
     * Attempt to get the cached expression tree of the supplied function reflection
     * 
     * @param string $FunctionHash
     * @return FunctionExpressionTree|null
     */
    public function TryGet($FunctionHash);
    
    /**
     * Removes the cached expression tree for the supplied function reflection
     * 
     * @param string $FunctionHash
     * @return void
     */
    public function Remove($FunctionHash);
    
    /**
     * Clears all cached function for the supplied function reflection.
     * 
     * @return void
     */
    public function Clear();
}
