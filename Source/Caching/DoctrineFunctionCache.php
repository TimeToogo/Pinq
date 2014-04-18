<?php

namespace Pinq\Caching;

use \Doctrine\Common\Cache\Cache;
use \Pinq\FunctionExpressionTree;

class DoctrineFunctionCache implements IFunctionCache
{    
    /**
     * @var Cache 
     */
    private $DoctrineCache;
    
    public function __construct(Cache $DoctrineCache)
    {
        $this->DoctrineCache = $DoctrineCache;
    }

    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree)
    {
        $this->DoctrineCache->save($FunctionHash, clone $FunctionExpressionTree);
    }

    public function TryGet($FunctionHash)
    {
        $Result = $this->DoctrineCache->fetch($FunctionHash) ;
        
        return $Result === false ? null : $Result;
    }
    
    public function Remove($FunctionHash)
    {
        $this->DoctrineCache->delete($FunctionHash);
    }

    public function Clear()
    {
        if($this->DoctrineCache instanceof \Doctrine\Common\Cache\CacheProvider) {
            $this->DoctrineCache->deleteAll();
        }
    }
}
