<?php

namespace Pinq\Expressions;

/**
 * <code>
 * unset($I, $B)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnsetExpression extends VariadicLanguageConstructExpression
{
    public function simplifyToValue(IEvaluationContext $context = null)
    {
        throw static::cannotSimplifyToValue();
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkUnset($this);
    }

    protected function updateValues(array $values)
    {
        return new self($values);
    }

    protected function compileCode(&$code)
    {
        $code .= 'unset(';
        $this->compileParameters($code);
        $code .= ')';
    }
}
