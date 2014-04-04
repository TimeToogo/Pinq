<?php

namespace Pinq\Queries;

interface IQueryStream extends \IteratorAggregate
{
    /**
     * @return IQuery[]
     */
    public function GetQueries();
    
    /**
     * @return boolean
     */
    public function IsEmpty();
    
    /**
     * @return IQueryStream
     */
    public function Append(IQuery $Query);
    
    /**
     * @return IQueryStream
     */
    public function Update(array $Queries);
    
    /**
     * @return IQueryStream
     */
    public function UpdateLast(IQuery $Query);
}
