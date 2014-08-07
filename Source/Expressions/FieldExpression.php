<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I->Field
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FieldExpression extends ObjectOperationExpression
{
    /**
     * @var Expression
     */
    private $name;

    public function __construct(Expression $value, Expression $name)
    {
        parent::__construct($value);
        $this->name = $name;
    }

    /**
     * @return Expression
     */
    public function getName()
    {
        return $this->name;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkField($this);
    }

    /**
     * @param Expression $value
     * @param Expression $name
     *
     * @return self
     */
    public function update(Expression $value, Expression $name)
    {
        if ($this->value === $value
                && $this->name === $name
        ) {
            return $this;
        }

        return new self($value, $name);
    }

    protected function updateValueExpression(Expression $value)
    {
        return new self($value, $this->name);
    }

    protected function compileCode(&$code)
    {
        $this->value->compileCode($code);
        $code .= '->';

        if ($this->name instanceof ValueExpression
                && self::isNormalSyntaxName($this->name->getValue())
        ) {
            $code .= $this->name->getValue();
        } else {
            $code .= '{';
            $this->name->compileCode($code);
            $code .= '}';
        }
    }

    public function dataToSerialize()
    {
        return $this->name;
    }

    public function unserializeData($data)
    {
        $this->name = $data;
    }

    public function __clone()
    {
        $this->value = clone $this->value;
        $this->name  = clone $this->name;
    }
}
