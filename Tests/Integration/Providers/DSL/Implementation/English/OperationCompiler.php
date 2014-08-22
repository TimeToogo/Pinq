<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation;
use Pinq\Queries\Operations;
use Pinq\Queries;

class OperationCompiler extends Compilation\OperationCompiler
{
    /**
     * @var CompiledQuery
     */
    protected $compilation;

    public function visitApply(Operations\Apply $operation)
    {
        $this->compilation->append('Update the values according to the function: ');
        $this->compilation->appendFunction($operation->getMutatorFunction());
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        $this->compilation->append('Join with: ');
        $this->compilation->appendJoinOptions($this->scopeCompiler, $this->parameters, $operation->getOptions());

        $this->compilation->append(' and update the outer values according to: ');
        $this->compilation->appendFunction($operation->getMutatorFunction());
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        $this->compilation->append('Add the following values: ');
        $this->compilation->appendSource($this->scopeCompiler, $this->parameters, $operation->getSource());
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        $this->compilation->append('Remove the following values: ');
        $this->compilation->appendSource($this->scopeCompiler, $this->parameters, $operation->getSource());
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
