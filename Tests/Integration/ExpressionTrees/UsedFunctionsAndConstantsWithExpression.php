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

    const SOME_ARRAY = [1,2,3] + ['abc' => 4, 'ddd' => 4, -5 => M_PI];

    class UsedFunctionsAndConstantsWithExpression
    {
        const TYPE = __CLASS__;

        const SOME_ARRAY = [1, 2, 3, 'foo' => '1.1.1.1', 4];

        public static function usedFunction()
        {
            return aliasedFunction();
        }

        public static function usedConstantWithScalarExpression()
        {
            return ALIASED_CONST_WITH_EXPRESSION;
        }

        public static function classConstantArrayDereference()
        {
            return self::SOME_ARRAY['foo'];
        }

        public static function constantArrayDereference()
        {
            return SOME_ARRAY[-5];
        }
    }
}