<?php

namespace Pinq\Providers\Collection;

use \Pinq\Queries;

/**
 * Repository provider for evalating query of the supplied collection instance,
 * this is useful for mocking a repository against an in memory collection.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\RepositoryProvider
{
    private $Collection;
    private $TraversableProvider;
    private $ScopeEvaluator;
    
    public function __construct(\Pinq\ICollection $Collection)
    {
        parent::__construct();
        $this->Collection = $Collection;
        $this->ScopeEvaluator = new \Pinq\Providers\Traversable\ScopeEvaluator();
        $this->TraversableProvider = new \Pinq\Providers\Traversable\Provider($Collection);
    }
    
    protected function LoadOperationEvaluatorVisitor(Queries\IScope $Scope)
    {
        $this->ScopeEvaluator->SetTraversable($this->Collection);
        $this->ScopeEvaluator->Walk($Scope);

        return new OperationEvaluator($this->ScopeEvaluator->GetTraversable()->AsCollection());
    }
    
    public function Load(Queries\IRequestQuery $Query)
    {
        return $this->TraversableProvider->Load($Query);
    }
    protected function LoadRequestEvaluatorVisitor(Queries\IScope $Scope) {}
}
