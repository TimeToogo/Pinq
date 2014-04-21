<?php

namespace Pinq\Providers\Traversable;

use \Pinq\Queries;

/**
 * Query provider for evalating query of the supplied traversable instance,
 * this is useful for mocking a queryable against an in memory traversable.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\QueryProvider
{
    private $ScopeEvaluator;
    protected $Traversable;

    public function __construct(\Pinq\ITraversable $Traversable)
    {
        parent::__construct();
        $this->ScopeEvaluator = new ScopeEvaluator();
        $this->Traversable = $Traversable;
    }
    
    
    protected function LoadRequestEvaluatorVisitor(Queries\IScope $Scope)
    {
        $this->ScopeEvaluator->SetTraversable($this->Traversable);
        $this->ScopeEvaluator->Walk($Scope);

        return new RequestEvaluator($this->ScopeEvaluator->GetTraversable());
    }
}
