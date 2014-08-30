<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Providers\DSL\Compilation\Processors\Visitors;
use Pinq\Queries;
use Pinq\Queries\Operations;
use Pinq\Queries\Requests;

/**
 * Implementation of the operation query processor to update function
 * expression trees of the query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationQueryProcessor extends Visitors\OperationQueryProcessor
{
    /**
     * @var ScopeProcessor
     */
    protected $scopeProcessor;

    /**
     * @var IExpressionProcessor
     */
    protected $expressionProcessor;

    public function __construct(IExpressionProcessor $expressionProcessor, Queries\IOperationQuery $operationQuery)
    {
        parent::__construct(new ScopeProcessor($expressionProcessor), $operationQuery);

        $this->expressionProcessor = $expressionProcessor;
    }

    public function visitApply(Operations\Apply $operation)
    {
        parent::visitApply(
                $operation->update($this->expressionProcessor->processFunction($operation->getMutatorFunction()))
        );
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        parent::visitJoinApply(
                $operation->update(
                        $this->scopeProcessor->updateJoinOptions($operation->getOptions()),
                        $this->expressionProcessor->processFunction($operation->getMutatorFunction())
                )
        );
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        parent::visitAddValues($operation->update($this->scopeProcessor->processSource($operation->getSource())));
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        parent::visitRemoveValues($operation->update($this->scopeProcessor->processSource($operation->getSource())));
    }

    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        parent::visitRemoveWhere(
                $operation->update($this->expressionProcessor->processFunction($operation->getPredicateFunction()))
        );
    }

}