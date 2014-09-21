<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Queries;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Interface of the expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IExpressionProcessor
{
    /**
     * Gets the original scope object.
     *
     * @return Queries\IScope
     */
    public function getScope();

    /**
     * Returns a new expression processor for the supplied sub scope.
     *
     * @param Queries\IScope $scope
     *
     * @return IExpressionProcessor
     */
    public function forSubScope(Queries\IScope $scope);

    /**
     * Adds a processed/updated segment to the scope.
     *
     * @param Queries\ISegment $segment
     *
     * @return void
     */
    public function addSegment(Queries\ISegment $segment);

    /**
     * Builds the processed/updated scope.
     *
     * @return Queries\IScope
     */
    public function buildScope();

    /**
     * Processes/updates the supplied function's expression.
     *
     * @param FunctionBase $function
     *
     * @return FunctionBase
     */
    public function processFunction(FunctionBase $function);
}
