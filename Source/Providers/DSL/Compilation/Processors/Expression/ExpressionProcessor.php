<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Expressions\ExpressionWalker;
use Pinq\Queries;
use Pinq\Queries\Functions\IFunction;

/**
 * Base class of expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ExpressionProcessor extends ExpressionWalker implements IExpressionProcessor
{
    /**
     * {@inheritDoc}
     */
    public function processFunction(IFunction $function)
    {
        return $function->update(
                $function->getScopeType(),
                $function->getNamespace(),
                $function->getParameterScopedVariableMap(),
                $this->walkAll($function->getParameters()->getAll()),
                $this->walkAll($function->getBodyExpressions())
        );
    }
}
