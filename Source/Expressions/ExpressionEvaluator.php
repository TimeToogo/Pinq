<?php

namespace Pinq\Expressions;

use Pinq\Expressions as O;

/**
 * Evaluates expression trees.
 * Example:
 * <code>
 * -2 + 4
 * </code>
 * Will become:
 * <code>
 * 2
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionEvaluator
{
    private function __construct()
    {

    }

    public static function evaluate(array $expressions, O\IEvaluationContext $context = null)
    {
        $thisObject           = null;
        $scope                = null;
        $contextParameterName = '__contextVariables__';
        $variableTable        = [];
        $namespace            = null;

        if ($context !== null) {
            $namespace     = $context->getNamespace();
            $scope         = $context->getScopeType();
            $thisObject    = $context->getThis();
            $variableTable = $context->getVariableValueMap();
        }

        $bodyCode = '';
        foreach ($variableTable as $variable => $value) {
            $variableName = O\Expression::value($variable);
            $variableCode = O\Expression::variable($variableName)->compile();
            $bodyCode .= "$variableCode =& $$contextParameterName" . '[' . $variableName->compile() . '];';
        }

        $bodyCode .= "unset($$contextParameterName);";
        $bodyCode .= implode(';', O\Expression::compileAll($expressions)) . ';';

        $code = <<<PHP
namespace {$namespace} {
    return function ($$contextParameterName)
    {
        $bodyCode
    };
}
PHP;

        //Have to actually eval the closure in an instance method due to strange semantics
        //making the closure static such that it cannot be bound.
        //Bug: https://bugs.php.net/bug.php?id=65598
        $closure = (new self())->evaluateInInstanceContext($code);

        $closure = $closure->bindTo($thisObject, $scope);

        return $closure($variableTable);
    }

    protected function evaluateInInstanceContext($code)
    {
        return eval($code);
    }
}
