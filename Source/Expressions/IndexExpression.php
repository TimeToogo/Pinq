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
     * @var Expression|null
     */
    private $index;

    public function __construct(Expression $value, Expression $index = null)
    {
        parent::__construct($value);
        $this->index = $index;
    }

    /**
     * @return boolean
     */
    public function hasIndex()
    {
        return $this->index !== null;
    }

    /**
     * @return Expression|null
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
     * @param Expression      $value
     * @param Expression|null $index
     *
     * @return self
     */
    public function update(Expression $value, Expression $index = null)
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
        if ($this->index !== null) {
            $this->index->compileCode($code);
        }
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
        $this->index = $this->index !== null ? clone $this->index : null;
    }
}
