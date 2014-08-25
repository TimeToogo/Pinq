<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries\Operations;
use Pinq\Queries;

class OperationCompiler extends Processors\Visitors\OperationQueryProcessor
{
    /**
     * @var CompiledQuery
     */
    protected $compilation;

    public function __construct(CompiledQuery $compiledQuery, Queries\IOperationQuery $operationQuery)
    {
        parent::__construct(new ScopeCompiler($compiledQuery, $operationQuery->getScope()), $operationQuery->getOperation());
        $this->compilation = $compiledQuery;
    }

    protected function processOperation(Queries\IScope $scope, Queries\IOperation $operation)
    {
        parent::processOperation($scope, $operation);
        return $operation;
    }

    public function visitApply(Operations\Apply $operation)
    {
        $this->compilation->append('Update the values according to the function: ');
        $this->compilation->appendFunction($operation->getMutatorFunction());
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        $this->compilation->append('Join with: ');
        $this->compilation->appendJoinOptions($operation->getOptions());

        $this->compilation->append(' and update the outer values according to: ');
        $this->compilation->appendFunction($operation->getMutatorFunction());
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        $this->compilation->append('Add the following values: ');
        $this->compilation->appendSource($operation->getSource());
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        $this->compilation->append('Remove the following values: ');
        $this->compilation->appendSource($operation->getSource());
    }

    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        $this->compilation->append('Remove the elements according to: ');
        $this->compilation->appendFunction($operation->getPredicateFunction());
    }

    public function visitClear(Operations\Clear $operation)
    {
        $this->compilation->append('Remove all the elements');
    }

    public function visitUnsetIndex(Operations\UnsetIndex $operation)
    {
        $this->compilation->append('Remove the index');
    }

    public function visitSetIndex(Operations\SetIndex $operation)
    {
        $this->compilation->append('Set the index');
    }
}
