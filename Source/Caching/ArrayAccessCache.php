<?php 

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * Adapter class for a cache that implements \ArrayAccess
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayAccessCache implements IFunctionCache
{
    /**
     * The cache object implementing array access
     * 
     * @var \ArrayAccess 
     */
    private $arrayAccess;
    
    public function __construct(\ArrayAccess $innerCache)
    {
        $this->arrayAccess = $innerCache;
    }
    
    public function save($functionHash, FunctionExpressionTree $functionExpressionTree)
    {
        $this->arrayAccess[$functionHash] = clone $functionExpressionTree;
    }
    
    public function tryGet($functionHash)
    {
        return isset($this->arrayAccess[$functionHash]) ? clone $this->arrayAccess[$functionHash] : null;
    }
    
    public function remove($functionHash)
    {
        unset($this->arrayAccess[$functionHash]);
    }
    
    public function clear()
    {
        if (method_exists($this->arrayAccess, 'Clear')) {
            $this->arrayAccess->clear();
        }
        else if ($this->arrayAccess instanceof \Traversable) {
            $keys = array_keys(iterator_to_array($this->arrayAccess, true));
            
            foreach ($keys as $key) {
                unset($this->arrayAccess[$key]);
            }
        }
    }
}