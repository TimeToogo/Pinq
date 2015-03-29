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
        $this->operation = $operation->traverse($this);

        return $this->operation;
    }

    public function visitUnsetIndex(Operations\UnsetIndex $operation)
    {
        return $operation;
    }

    public function visitApply(Operations\Apply $operation)
    {
        return $operation;
    }

    public function visitClear(Operations\Clear $operation)
    {
        return $operation;
    }

    public function visitSetIndex(Operations\SetIndex $operation)
    {
        return $operation;
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        return $operation->update(
                $this->scopeProcessor->processSource($operation->getSource())
        );
    }

    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        return $operation;
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        return $operation->update(
                $operation->getOptions()->updateSource(
                        $this->scopeProcessor->processSource($operation->getOptions()->getSource())
                ),
                $operation->getMutatorFunction()
        );
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        return $operation->update(
                $this->scopeProcessor->processSource($operation->getSource())
        );
    }
}
