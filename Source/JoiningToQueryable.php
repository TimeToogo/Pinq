<?php

namespace Pinq;

use \Pinq\Queries;

class JoiningToQueryable implements IJoiningToTraversable
{
    /**
     * @var Providers\IQueryProvider
     */
    private $Provider;
    
    /**
     * @var Queries\IScope
     */
    private $Scope;
    
    /**
     * @var callable
     */
    private $ConstructSegmentFunction;
    
    public function __construct(
            Providers\IQueryProvider $Provider, 
            Queries\IScope $Scope,
            callable $ConstructSegmentFunction)
    {
        $this->Provider = $Provider;
        $this->Scope = $Scope;
        $this->ConstructSegmentFunction = $ConstructSegmentFunction;
    }

    public function To(callable $JoinFunction)
    {
        $ConstructSegmentFunction = $this->ConstructSegmentFunction;
        return $this->Provider->CreateQueryable($this->Scope->Append($ConstructSegmentFunction($JoinFunction)));
    }
}
