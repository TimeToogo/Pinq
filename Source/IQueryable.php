<?php

namespace Pinq;

/**
 * Concrete classes should be immutable and return a new instance with every chained query call.
 */
interface IQueryable extends ITraversable
{
    /**
     * @return Providers\IQueryProvider
     */
    public function GetProvider();
    
    /**
     * @return Providers\IQueryScope
     */
    public function GetQueryScope();
    
    /**
     * @return Queries\IQueryStream
     */
    public function GetQueryStream();
}
