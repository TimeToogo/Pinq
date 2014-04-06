<?php

namespace Pinq\Providers;

use \Pinq\Queries;

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
     * @return Queries\Operations\OperationVisitor
     */
    protected abstract function LoadOperationEvaluatorVisitor(Queries\IScope $Scope);

}
