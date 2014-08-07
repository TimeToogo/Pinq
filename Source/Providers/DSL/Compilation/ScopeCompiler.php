<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\PinqException;
use Pinq\Queries;
use Pinq\Queries\Segments;

/**
 * Base class of a scope compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ScopeCompiler extends Segments\SegmentVisitor implements IScopeCompiler
{
    use CompilerProperties;

    public function compileScope(
            $compilation,
            Queries\IScope $scope,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        $this->runCompile(
                $compilation,
                $structuralParameters,
                function () use ($scope) {
                    $this->visit($scope);
                }
        );
    }

    public function visitOperation(Segments\Operation $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitRange(Segments\Range $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitUnique(Segments\Unique $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitFilter(Segments\Filter $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitGroupBy(Segments\GroupBy $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitJoin(Segments\Join $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitOrderBy(Segments\OrderBy $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitSelect(Segments\Select $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitSelectMany(Segments\SelectMany $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitIndexBy(Segments\IndexBy $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitKeys(Segments\Keys $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function visitReindex(Segments\Reindex $query)
    {
        throw PinqException::notSupported(__METHOD__);
    }
}