<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

class SecondLevelFunctionCache implements IFunctionCache
{
    /**
     * @var IFunctionCache
     */
    private $RealCache;

    private static $ArrayCache = [];
    
    public function __construct(IFunctionCache $InnerCache)
    {
        $this->RealCache = $InnerCache;
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
