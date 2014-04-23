<?php 

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * Cache implementation that acts as second level cache for the supplied
 * implementation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SecondLevelFunctionCache implements IFunctionCache
{
    /**
     * The underlying cache implementation
     * 
     * @var IFunctionCache
     */
    private $realCache;
    
    private static $arrayCache = [];
    
    public function __construct(IFunctionCache $innerCache)
    {
        $this->realCache = $innerCache;
    }
    
    /**
     * Gets the underlying cache implementation
     * 
     * @return IFunctionCache
     */
    public function getInnerCache()
    {
        return $this->realCache;
    }
    
    public function save($functionHash, FunctionExpressionTree $functionExpressionTree)
    {
        self::$arrayCache[$functionHash] = clone $functionExpressionTree;
        $this->realCache->save($functionHash, $functionExpressionTree);
    }
    
    public function tryGet($functionHash)
    {
        if (!isset(self::$arrayCache[$functionHash])) {
            self::$arrayCache[$functionHash] = $this->realCache->tryGet($functionHash) ?: null;
        }
        
        $cachedValue = self::$arrayCache[$functionHash];
        
        return $cachedValue === null ? null : clone $cachedValue;
    }
    
    public function clear()
    {
        self::$arrayCache = [];
        $this->realCache->clear();
    }
    
    public function remove($functionHash)
    {
        unset(self::$arrayCache[$functionHash]);
        $this->realCache->remove($functionHash);
    }
}