<?php

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ObjectOperationExpression extends TraversalExpression
{
    public function __construct(Expression $value)
    {
        parent::__construct($value);
    }
}
