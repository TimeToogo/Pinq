<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\PinqException;
use Pinq\Queries;
use Pinq\Queries\Requests;

/**
 * Base class of a scope compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RequestCompiler extends Requests\RequestVisitor implements IRequestCompiler
{
    use CompilerPropertiesWithScope;

    public function compileRequest(
            $compilation,
            IScopeCompiler $scopeCompiler,
            Queries\IRequest $request,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        $this->runCompileWithScope(
                $compilation,
                $scopeCompiler,
                $structuralParameters,
                function () use ($request) {
                    $this->visit($request);
                }
        );
    }

    public function visitValues(Requests\Values $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitCount(Requests\Count $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitIsEmpty(Requests\IsEmpty $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitFirst(Requests\First $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitLast(Requests\Last $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitContains(Requests\Contains $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitAggregate(Requests\Aggregate $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitMaximum(Requests\Maximum $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitMinimum(Requests\Minimum $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitSum(Requests\Sum $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitAverage(Requests\Average $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitAll(Requests\All $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitAny(Requests\Any $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitImplode(Requests\Implode $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitGetIndex(Requests\GetIndex $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitIssetIndex(Requests\IssetIndex $request)
    {
        throw PinqException::notSupported(__METHOD__);
    }
}