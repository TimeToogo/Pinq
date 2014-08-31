<?php

namespace Some\Other\Name\Space {
    const SOME_CONST_WITH_EXPRESSION = 'expression' . '1,2,34';
    function someFunction() {
        return __FUNCTION__;
    }
}

namespace Pinq\Tests\Integration\ExpressionTrees {

    use function Some\Other\Name\Space\someFunction as aliasedFunction;
    use const Some\Other\Name\Space\SOME_CONST_WITH_EXPRESSION as ALIASED_CONST_WITH_EXPRESSION;


    class UsedFunctionsAndConstantsWithExpression
    {
        const TYPE = __CLASS__;

        public static function usedFunction()
        {
            return aliasedFunction();
        }

        public static function usedConstantWithScalarExpression()
        {
            return ALIASED_CONST_WITH_EXPRESSION;
        }
    }
}