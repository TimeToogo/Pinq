<?php

namespace Pinq\Providers;

use \Pinq\Queries;

/**
 * Base class for the repository provider, with default functionality
 * for request and optionary query evaluation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class RepositoryProvider extends QueryProvider implements IRepositoryProvider
{
    public function CreateRepository(Queries\IScope $Scope = null)
    {
        return new \Pinq\Repository($this, $Scope);
    }

    public function Execute(Queries\IOperationQuery $Query)
    {
        $this->LoadOperationEvaluatorVisitor($Query->GetScope())->Visit($Query->GetOperation());
    }
    /**
     * This should be implemented such that it returns an operation visitor
     * which will execute the supplied operation query
     * 
     * @return Queries\Operations\OperationVisitor
     */
    protected abstract function LoadOperationEvaluatorVisitor(Queries\IScope $Scope);

}
