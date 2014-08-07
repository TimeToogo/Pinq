<?php

namespace Pinq\Expressions;

/**
 * <code>
 * isset($var, $other)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IssetExpression extends VariadicLanguageConstructExpression
{
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkIsset($this);
    }

    protected function updateValues(array $values)
    {
        return new self($values);
    }

    protected function compileCode(&$code)
    {
        $code .= 'isset(';
        $this->compileParameters($code);
        $code .= ')';
    }
}
