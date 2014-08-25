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
     * @var Queries\IOperation
     */
    private $operation;

    public function __construct(IScopeProcessor $scopeProcessor, Queries\IOperation $operation)
    {
        parent::__construct($scopeProcessor);
        $this->operation = $operation;
    }

    public function buildQuery()
    {
        $scope = $this->scopeProcessor->buildScope();
        return new Queries\OperationQuery($this->processScope($scope, $this->operation), $this->processOperation(
                $scope,
                $this->operation
        ));
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