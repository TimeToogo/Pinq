<?php 

namespace Pinq\Providers;

use Pinq\Queries;

/**
 * Base class for the repository provider, with default functionality
 * for request and optionary query evaluation.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class RepositoryProvider extends QueryProvider implements IRepositoryProvider
{
    public function createRepository(Queries\IScope $scope = null)
    {
        return new \Pinq\Repository($this, $scope);
    }
    
    public function execute(Queries\IOperationQuery $query)
    {
        $this->loadOperationEvaluatorVisitor($query->getScope())->visit($query->getOperation());
    }
    
    /**
     * This should be implemented such that it returns an operation visitor
     * which will execute the supplied operation query
     * 
     * @return Queries\Operations\OperationVisitor
     */
    protected abstract function loadOperationEvaluatorVisitor(Queries\IScope $scope);
}