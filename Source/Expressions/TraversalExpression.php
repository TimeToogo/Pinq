<?php

namespace Pinq\Expressions;

/**
 * Represents acting on a value (properties, methods, indexer...)
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class TraversalExpression extends Expression
{
    /**
     * @var Expression
     */
    protected $ValueExpression;

    /**
     * @var Expression
     */
    protected $OriginExpression;

    /**
     * @var int
     */
    protected $TraversalDepth;

    public function __construct(Expression $ValueExpression)
    {
        $this->ValueExpression = $ValueExpression;

        if ($ValueExpression instanceof self) {
            $this->OriginExpression = $ValueExpression->OriginExpression;
            $this->TraversalDepth = $ValueExpression->TraversalDepth + 1;
        } 
        else {
            $this->OriginExpression = $ValueExpression;
            $this->TraversalDepth = 1;
        }
    }

    /**
     * @param string $ExpressionType
     * @return boolean
     */
    final public function OriginatesFrom($ExpressionType)
    {
        return $this->OriginExpression instanceof $ExpressionType;
    }

    /**
     * @return Expression
     */
    final public function GetOriginExpression()
    {
        return $this->OriginExpression;
    }

    /**
     * @return int
     */
    final public function GetTraversalDepth()
    {
        return $this->TraversalDepth;
    }

    /**
     * @return Expression
     */
    final public function GetValueExpression()
    {
        return $this->ValueExpression;
    }

    /**
     * @return Expression
     */
    final public function UpdateValue(Expression $ValueExpression)
    {
        if ($this->ValueExpression === $ValueExpression) {
            return $this;
        }

        return $this->UpdateValueExpression($ValueExpression);
    }
    abstract protected function UpdateValueExpression(Expression $ValueExpression);
}
