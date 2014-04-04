<?php

namespace Pinq\Queries;

class QueryStream implements IQueryStream
{
    /**
     * @var IQuery[]
     */
    private $Queries = [];

    public function __construct(array $Stream)
    {
        $this->Queries = $Stream;
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->Queries);
    }
    
    /**
     * @return IQuery[]
     */
    public function GetQueries()
    {
        return $this->Queries;
    }
    
    public function IsEmpty()
    {
        return empty($this->Queries);
    }
    
    public function Append(IQuery $Query)
    {
        return new self(array_merge($this->Queries, [$Query]));
    }
    
    public function Update(array $Queries)
    {
        if($this->Queries === $Queries) {
            return $this;
        }
        
        return new self($Queries);
    }
    
    public function UpdateLast(IQuery $Query)
    {
        if(end($this->Queries) === $Query) {
            return $this;
        }
        
        return $this->Append($Query);
    }
}
