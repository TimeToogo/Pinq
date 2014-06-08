<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ModifierFunction extends FunctionBase
{
    /**
     * @var O\AssignmentExpression[]
     */
    protected $modifierExpressions;

    public function __construct(callable $function, O\ClosureExpression $functionExpression)
    {
        parent::__construct($function, $functionExpression);
        
        foreach($this->functionExpression->getBodyExpressions() as $expression) {
            if($expression instanceof O\AssignmentExpression) {
                $this->modifierExpressions[] = $expression;
            }
        }
    }

    /**
     * @return O\AssignmentExpression[]
     */
    final public function getModifierExpressions()
    {
        return $this->modifierExpressions;
    }
}
