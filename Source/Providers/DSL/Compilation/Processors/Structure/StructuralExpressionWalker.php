<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Functions\IFunction;

/**
 * Implementation of the structural expression walker.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionWalker extends O\ExpressionWalker
{
    /**
     * @var callable
     */
    protected $processParameterCallback;

    /**
     * @var IFunction
     */
    protected $function;

    /**
     * @var IStructuralExpressionProcessor
     */
    protected $processor;

    public function __construct(
            callable $processParameterCallback,
            IFunction $function,
            IStructuralExpressionProcessor $processors
    ) {
        $this->processParameterCallback = $processParameterCallback;
        $this->function                 = $function;
        $this->processor               = $processors;
    }

    protected function doWalk(O\Expression $expression)
    {
        if ($this->processor->matches($this->function, $expression)) {
            $processParameterCallback = $this->processParameterCallback;
            $expression = $processParameterCallback($this->processor, $expression);
        }

        return parent::doWalk($expression);
    }
}
