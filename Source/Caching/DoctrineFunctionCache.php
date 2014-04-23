<?php 

namespace Pinq\Caching;

use Doctrine\Common\Cache\Cache;
use Pinq\FunctionExpressionTree;

/**
 * Adapter class for a doctring cache component that implements
 * \Doctrine\Common\Cache\Cache
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DoctrineFunctionCache implements IFunctionCache
{
    /**
     * The doctrine cache implementation
     * 
     * @var Cache 
     */
    private $doctrineCache;
    
    public function __construct(Cache $doctrineCache)
    {
        $this->doctrineCache = $doctrineCache;
    }
    
    public function save($functionHash, FunctionExpressionTree $functionExpressionTree)
    {
        $this->doctrineCache->save(
                $functionHash,
                clone $functionExpressionTree);
    }
    
    public function tryGet($functionHash)
    {
        $result = $this->doctrineCache->fetch($functionHash);
        
        return $result === false ? null : $result;
    }
    
    public function remove($functionHash)
    {
        $this->doctrineCache->delete($functionHash);
    }
    
    public function clear()
    {
        if ($this->doctrineCache instanceof \Doctrine\Common\Cache\CacheProvider) {
            $this->doctrineCache->deleteAll();
        }
    }
}