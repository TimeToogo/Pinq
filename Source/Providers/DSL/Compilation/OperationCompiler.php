<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\PinqException;
use Pinq\Queries\Operations;
use Pinq\Queries;

/**
 * Base class of a scope compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OperationCompiler extends Operations\OperationVisitor implements IOperationCompiler
{
    use CompilerPropertiesWithScope;

    public function compileOperation(
            $compilation,
            IScopeCompiler $scopeCompiler,
            Queries\IOperation $operation,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        $this->runCompileWithScope(
                $compilation,
                $scopeCompiler,
                $structuralParameters,
                function () use ($operation) {
                    $this->visit($operation);
                }
        );
    }

    public function visitApply(Operations\Apply $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitJoinApply(Operations\JoinApply $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitAddValues(Operations\AddValues $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitRemoveValues(Operations\RemoveValues $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitRemoveWhere(Operations\RemoveWhere $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitClear(Operations\Clear $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitUnsetIndex(Operations\UnsetIndex $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitSetIndex(Operations\SetIndex $operation)
    {
        throw PinqException::notSupported(__METHOD__);
    }
}