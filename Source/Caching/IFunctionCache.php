<?php 

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

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
     * @param string $functionHash
     * @param FunctionExpressionTree $functionExpressionTree
     * @return void
     */
    public function save($functionHash, FunctionExpressionTree $functionExpressionTree);
    
    /**
     * Attempt to get the cached expression tree of the supplied function reflection
     * 
     * @param string $functionHash
     * @return FunctionExpressionTree|null
     */
    public function tryGet($functionHash);
    
    /**
     * Removes the cached expression tree for the supplied function reflection
     * 
     * @param string $functionHash
     * @return void
     */
    public function remove($functionHash);
    
    /**
     * Clears all cached function for the supplied function reflection.
     * 
     * @return void
     */
    public function clear();
}