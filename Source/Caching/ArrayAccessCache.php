<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

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
    private $ArrayAccess;
    
    public function __construct(\ArrayAccess $InnerCache)
    {
        $this->ArrayAccess = $InnerCache;
    }

    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree)
    {
        $this->ArrayAccess[$FunctionHash] = clone $FunctionExpressionTree;
    }

    public function TryGet($FunctionHash)
    {
        return isset($this->ArrayAccess[$FunctionHash]) ? clone $this->ArrayAccess[$FunctionHash] : null;
    }
    
    public function Remove($FunctionHash)
    {
        unset($this->ArrayAccess[$FunctionHash]);
    }

    public function Clear()
    {
        if(method_exists($this->ArrayAccess, 'Clear')) {
            $this->ArrayAccess->Clear();
        }
        else if ($this->ArrayAccess instanceof \Traversable) {
            $Keys = array_keys(iterator_to_array($this->ArrayAccess, true));
            foreach($Keys as $Key) {
                unset($this->ArrayAccess[$Key]);
            }
        }
    }
}
