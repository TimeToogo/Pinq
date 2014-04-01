<?php

namespace Pinq\Providers\Base;

use \Pinq\Queries;

abstract class Provider implements \Pinq\IQueryProvider
{
    
    final public function LoadQueryScope(Queries\IQueryStream $QueryStream = null)
    {
        if ($QueryStream === null) {
            return $this;
        } 
        else {
            return $this->LoadQueryStreamScope($QueryStream);
        }
    }
    /**
     * @return static
     */
    abstract protected function LoadQueryStreamScope(Queries\IQueryStream $QueryStream);
}
