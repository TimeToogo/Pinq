<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Base class of the operation query processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OperationQueryProcessor extends QueryProcessor implements IOperationQueryProcessor
{
    /**
     * @var Queries\IOperationQuery
     */
    private $operationQuery;

    public function __construct(IScopeProcessor $scopeProcessor, Queries\IOperationQuery $operationQuery)
    {
        parent::__construct($scopeProcessor);
        $this->operationQuery = $operationQuery;
    }

    public function buildQuery()
    {
        $scope     = $this->scopeProcessor->buildScope();
        $operation = $this->operationQuery->getOperation();

        return $this->operationQuery->update(
                $this->processScope($scope, $operation),
                $this->processOperation(
                        $scope,
                        $operation
                )
        );
    }

    /**
     * @param Queries\IScope     $scope
     * @param Queries\IOperation $operation
     *
     * @return Queries\IScope
     */
    protected function processScope(Queries\IScope $scope, Queries\IOperation $operation)
    {
        return $scope;
    }

    /**
     * @param Queries\IScope     $scope
     * @param Queries\IOperation $operation
     *
     * @return Queries\IOperation
     */
    protected function processOperation(Queries\IScope $scope, Queries\IOperation $operation)
    {
        return $operation;
    }
}
