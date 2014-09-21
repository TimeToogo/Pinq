<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $var->method($one, true)
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MethodCallExpression extends ObjectOperationExpression
{
    /**
     * @var Expression
     */
    private $name;

    /**
     * @var ArgumentExpression[]
     */
    private $arguments;

    public function __construct(Expression $value, Expression $name, array $arguments = [])
    {
        parent::__construct($value);
        $this->name      = $name;
        $this->arguments = self::verifyAll($arguments, ArgumentExpression::getType());
    }

    /**
     * @return Expression
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArgumentExpression[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkMethodCall($this);
    }

    /**
     * @param Expression           $value
     * @param Expression           $name
     * @param ArgumentExpression[] $arguments
     *
     * @return MethodCallExpression
     */
    public function update(
            Expression $value,
            Expression $name,
            array $arguments
    ) {
        if ($this->value === $value
                && $this->name === $name
                && $this->arguments === $arguments
        ) {
            return $this;
        }

        return new self(
                $value,
                $name,
                $arguments);
    }

    protected function updateValueExpression(Expression $value)
    {
        return new self(
                $value,
                $this->name,
                $this->arguments);
    }

    protected function compileCode(&$code)
    {
        $this->value->compileCode($code);
        $code .= '->';

        if ($this->name instanceof ValueExpression && self::isNormalSyntaxName($this->name->getValue())) {
            $code .= $this->name->getValue();
        } else {
            $code .= '{';
            $this->name->compileCode($code);
            $code .= '}';
        }

        $code .= '(';
        $code .= implode(',', self::compileAll($this->arguments));
        $code .= ')';
    }

    public function dataToSerialize()
    {
        return [$this->name, $this->arguments];
    }

    public function unserializeData($data)
    {
        list($this->name, $this->arguments) = $data;
    }

    public function __clone()
    {
        $this->value     = clone $this->value;
        $this->name      = clone $this->name;
        $this->arguments = self::cloneAll($this->arguments);
    }
}
