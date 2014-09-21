<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Queries;

/**
 * Base class of expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ExpressionProcessor implements IExpressionProcessor
{
    /**
     * The original scope.
     *
     * @var Queries\IScope
     */
    protected $scope;

    /**
     * The processed segments.
     *
     * @var Queries\ISegment[]
     */
    protected $segments = [];

    public function __construct(Queries\IScope $scope)
    {
        $this->scope = $scope;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function addSegment(Queries\ISegment $segment)
    {
        $this->segments[] = $segment;
    }

    public function buildScope()
    {
        return $this->scope->updateSegments($this->segments);
    }
}
