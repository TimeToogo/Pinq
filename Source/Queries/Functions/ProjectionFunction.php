<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ProjectionFunctions extends FunctionBase
{
    /**
     * @var O\Expression|null
     */
    protected $firstReturnValueExpression;
    
    /**
     * @var O\Expression|null
     */
    protected $firstReturnValueExpression;

    public function __construct(callable $function, O\ClosureExpression $functionExpression)
    {
        parent::__construct($function, $functionExpression);
        
        foreach($this->functionExpression->getBodyExpressions() as $expression) {
            if($expression instanceof O\ReturnExpression) {
                $this->firstReturnValueExpression = $expression->getValueExpression();
                break;
            }
        }
    }

    /**
     * @return boolean
     */
    public function hasReturnExpression()
    {
        return $this->firstReturnValueExpression !== null;
    }

    /**
     * @return O\Expression|null
     */
    public function getFirstReturnValueExpression()
    {
        return $this->firstReturnValueExpression;
    }
}
