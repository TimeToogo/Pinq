<?php

namespace Pinq\Queries\Expression;

use \Pinq\Expressions\Expression;

abstract class FunctionQuery extends Query
{
    /**
     * @var Expression
     */
    private $Expression;

    public function __construct(Expression $Expression)
    {
        $this->Expression = $Expression;
    }

    /**
     * @return Expression
     */
    public function GetExpression()
    {
        return $this->Expression;
    }
}
