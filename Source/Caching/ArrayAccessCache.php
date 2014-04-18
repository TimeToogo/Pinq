<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

class ArrayAccessCache implements IFunctionCache
{    
    /**
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
