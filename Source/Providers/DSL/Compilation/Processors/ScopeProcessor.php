<?php

namespace Pinq\Providers\DSL\Compilation\Processors;

use Pinq\Queries;

/**
 * Implementation of the scope processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ScopeProcessor implements IScopeProcessor
{
    /**
     * @var Queries\IScope
     */
    protected $scope;

    public function __construct(Queries\IScope $scope)
    {
        $this->scope = $scope;
    }

    public function buildScope()
    {
        return new Queries\Scope($this->processSourceInfo($this->scope->getSourceInfo()), $this->processSegments($this->scope->getSegments()));
    }

    /**
     * @param Queries\ISourceInfo $sourceInfo
     *
     * @return Queries\ISourceInfo
     */
    protected function processSourceInfo(Queries\ISourceInfo $sourceInfo)
    {
        return $sourceInfo;
    }

    /**
     * @param Queries\ISegment[] $segments
     *
     * @return Queries\ISegment[]
     */
    protected function processSegments(array $segments)
    {
        return $segments;
    }
}
