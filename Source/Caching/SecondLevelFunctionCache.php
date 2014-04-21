<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

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
    private $RealCache;

    private static $ArrayCache = [];
    
    public function __construct(IFunctionCache $InnerCache)
    {
        $this->RealCache = $InnerCache;
    }
    
    /**
     * Gets the underlying cache implementation
     * 
     * @return IFunctionCache
     */
    public function GetInnerCache()
    {
        return $this->RealCache;
    }
    
    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree)
    {
        self::$ArrayCache[$FunctionHash] = clone $FunctionExpressionTree;
        $this->RealCache->Save($FunctionHash, $FunctionExpressionTree);
    }

    public function TryGet($FunctionHash)
    {
        if(!isset(self::$ArrayCache[$FunctionHash])) {
            self::$ArrayCache[$FunctionHash] = $this->RealCache->TryGet($FunctionHash) ?: null;
        }
        
        $CachedValue = self::$ArrayCache[$FunctionHash];
        return $CachedValue === null ? null : clone $CachedValue;
    }

    public function Clear()
    {
        self::$ArrayCache = [];
        $this->RealCache->Clear();
    }

    public function Remove($FunctionHash)
    {
        unset(self::$ArrayCache[$FunctionHash]);
        $this->RealCache->Remove($FunctionHash);
    }
}
