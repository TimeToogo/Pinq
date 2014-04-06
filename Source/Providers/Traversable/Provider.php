<?php

namespace Pinq\Providers\Traversable;

use \Pinq\Queries;

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
