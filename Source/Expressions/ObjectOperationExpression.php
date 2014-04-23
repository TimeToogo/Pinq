<?php 

namespace Pinq\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ObjectOperationExpression extends TraversalExpression
{
    public function __construct(Expression $objectValueExpression)
    {
        parent::__construct($objectValueExpression);
    }
}