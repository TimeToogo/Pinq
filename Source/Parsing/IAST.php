<?php

namespace Pinq\Parsing;

use \Pinq\Expressions\Expression;

interface IAST
{
    /**
     * @return Expression[]
     */
    public function GetExpressions();
}
