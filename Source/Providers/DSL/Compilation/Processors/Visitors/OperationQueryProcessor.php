<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Visitors;

use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries\Operations;
use Pinq\Queries;

/**
 * Implementation of the operation query processor using the visitor pattern.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationQueryProcessor extends Processors\OperationQueryProcessor implements Operations\IOperationVisitor
{
    /**
     * @var Queries\IOperation
     */
    protected $operation;

    protected function processOperation(Queries\IScope $scope, Queries\IOperation $operation)
    {
        $operation->traverse($this);

        return $this->operation;
    }

    public function visitUnsetIndex(Operations\UnsetIndex $operation)
    {
        $this->operation = $operation;
    }

    public function visitApply(Operations\Apply $operation)
    {
        $this->operation = $operation;
    }

    public function visitClear(Operations\Clear $operation)
    {
        $this->operation = $operation;
    }

    public function visitSetIndex(Operations\SetIndex $operation)
    {
        $this->operation = $operation;
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        $this->operation = $operation;
    }

    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        $this->operation = $operation;
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        $this->operation = $operation;
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        $this->operation = $operation;
    }
}