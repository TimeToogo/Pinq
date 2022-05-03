<?php

namespace Pinq\Expressions;

/**
 * <code>
 * [1, 2, 'test' => 4]
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayExpression extends Expression
{
    /**
     * @var ArrayItemExpression[]
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = self::verifyAll($items, ArrayItemExpression::getType());
    }

    /**
     * @return ArrayItemExpression[]
     */
    public function getItems()
    {
        return $this->items;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkArray($this);
    }

    /**
     * @param ArrayItemExpression[] $items
     *
     * @return self
     */
    public function update(array $items)
    {
        if ($this->items === $items) {
            return $this;
        }

        return new self($items);
    }

    protected function compileCode(&$code)
    {
        $code .= '[';
        $first = true;

        foreach ($this->items as $itemExpression) {
            if ($first) {
                $first = false;
            } else {
                $code .= ', ';
            }

            $itemExpression->compileCode($code);
        }

        $code .= ']';
    }

    public function serialize()
    {
        return serialize([$this->items]);
    }
    
    public function __serialize(): array
    {
        return [$this->items];
    }

    public function unserialize($serialized)
    {
        list($this->items) = unserialize($serialized);
    }
    
    public function __unserialize(array $data): void
    {
        list($this->items) = $data;
    }

    public function __clone()
    {
        $this->items = self::cloneAll($this->items);
    }
}
