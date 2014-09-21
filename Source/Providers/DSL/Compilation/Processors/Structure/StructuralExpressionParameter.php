<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionParameter;
use Pinq\Providers\DSL\Compilation\Parameters\IParameterHasher;
use Pinq\Queries\Functions\IFunction;

/**
 * Implementation of the structural expression parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionParameter extends ExpressionParameter
{
    /**
     * @var O\Expression
     */
    protected $expression;

    public function __construct(
            O\Expression $expression,
            IParameterHasher $hasher,
            IFunction $function = null,
            $data = null
    ) {
        parent::__construct($expression, $hasher, $function, $data);
        $this->expression = $expression;
    }

    /**
     * @return \Pinq\Expressions\Expression
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
