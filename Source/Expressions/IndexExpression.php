<?php

namespace Pinq\Expressions;

/**
 * <code>
 * $I[5]
 * </code>
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IndexExpression extends TraversalExpression
{
    /**
     * @var Expression
     */
    private $index;

    public function __construct(Expression $value, Expression $index)
    {
        parent::__construct($value);
        $this->index = $index;
    }

    /**
     * @return Expression
     */
    public function getIndex()
    {
        return $this->index;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkIndex($this);
    }

    /**
     * @param Expression $value
     * @param Expression $index
     *
     * @return self
     */
    public function update(Expression $value, Expression $index)
    {
        if ($this->value === $value && $this->index === $index) {
            return $this;
        }

        return new self($value, $index);
    }

    protected function updateValueExpression(Expression $value)
    {
        return new self($value, $this->index);
    }

    protected function compileCode(&$code)
    {
        $this->value->compileCode($code);
        $code .= '[';
        $this->index->compileCode($code);
        $code .= ']';
    }

    public function dataToSerialize()
    {
        return $this->index;
    }

    public function unserializeData($data)
    {
        $this->index = $data;
    }

    public function __clone()
    {
        $this->value = clone $this->value;
        $this->index = clone $this->index;
    }
}
