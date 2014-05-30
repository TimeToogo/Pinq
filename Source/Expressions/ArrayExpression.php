<?php

namespace Pinq\Expressions;

/**
 * <code>
 * [1, 2, 'test' => 4]
 * </code>
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayExpression extends Expression
{
    /**
     * @var ArrayItemExpression[]
     */
    private $itemExpressions;

    public function __construct(array $itemExpressions)
    {
        $this->itemExpressions = $itemExpressions;
    }

    /**
     * @return ArrayItemExpression[]
     */
    public function getItemExpressions()
    {
        return $this->itemExpressions;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkArray($this);
    }

    public function simplify()
    {
        $itemExpressions = self::simplifyAll($this->itemExpressions);
        
        $resolvedArray = [];
        foreach ($itemExpressions as $itemExpression) {
            $keyExpression = $itemExpression->getKeyExpression();
            $valueExpression = $itemExpression->getValueExpression();
            
            if(($keyExpression !== null && !($valueExpression instanceof ValueExpression))
                || !($valueExpression instanceof ValueExpression)) {
                return $this->update($itemExpressions);
            }
            
            if($keyExpression === null) {
                $resolvedArray[] = $valueExpression->getValue();
            } else {
                $resolvedArray[$keyExpression->getValue()] = $valueExpression->getValue();
            }
        }
        
        return Expression::value($resolvedArray);
    }

    /**
     * @return self
     */
    public function update(array $itemExpressions)
    {
        if ($this->itemExpressions === $itemExpressions) {
            return $this;
        }

        return new self($itemExpressions);
    }

    protected function compileCode(&$code)
    {
        $code .= '[';
        $first = true;

        foreach ($this->itemExpressions as $itemExpression) {
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
        return serialize([$this->itemExpressions]);
    }

    public function unserialize($serialized)
    {
        list($this->itemExpressions) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->itemExpressions = self::cloneAll($this->itemExpressions);
    }
}
