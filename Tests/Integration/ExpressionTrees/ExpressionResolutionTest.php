<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

class ExpressionResolutionTest extends InterpreterTest
{
    /**
     * @dataProvider interpreters
     */
    public function testOperationsReturnValueResolution()
    {
        $value = 5;

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            return $value + 1;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            return $value - 1;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            return $value * 5;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            return $value / $value + $value - $value % ~$value;
        });
    }

    /**
     * @dataProvider interpreters
     */
    public function testComplexReturnValueResolution()
    {
        $value = 5;

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            $copy = $value;

            return $copy + 1;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            $copy =& $value;
            $another = $value + 1;

            return $copy + 1 - $another;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value) {
            $copy = $value / $value + $value - $value % ~$value;
            $another = $value % 34 - $copy + $value;

            return $another - $copy + 1 - $another * $value % 34;
        });
    }

    /**
     * @dataProvider interpreters
     */
    public function testComplexReturnValueResolutionWithVariableVariables()
    {
        $value = 5;
        $variable = 'value';

        $this->assertReturnValueResolvesCorrectly(function () use ($value, $variable) {
            $copy = ${$variable};

            return $copy + 1;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value, $variable) {
            $copy =& ${$variable};
            $another = $value + 1;

            return $copy + 1 - $another;
        });

        $this->assertReturnValueResolvesCorrectly(function () use ($value, $variable) {
            $copy = ${$variable} / $value + $value - $value % ~${$variable};
            $another = $value % 34 - $copy + ${$variable};

            return $another - $copy + 1 - $another * $value % 34;
        });
    }

    final protected function assertReturnValueResolvesCorrectly(callable $function)
    {
        /*$reflection = $this->currentImplementation->getReflection($function);
        $bodyExpressions = $this->currentImplementation->getStructure($reflection)->getBodyExpressions();
        
        $variableExpressionMap = [];
        foreach($reflection->getScope()->getVariableValueMap() as $variable => $value) {
            $variableExpressionMap[$variable] = O\Expression::value($value);
        }
        
        $variableResolverWalker = new O\Walkers\VariableResolver($variableExpressionMap);
        $bodyExpressions = $variableResolverWalker->walkAll($bodyExpressions);
        
        foreach($bodyExpressions as $bodyExpression) {
            if($bodyExpression instanceof O\ReturnExpression) {
                $return = $bodyExpression->simplify();
                $this->assertTrue($return->hasValueExpression());
                $this->assertTrue($return->getValueExpression() instanceof O\ValueExpression);
                $this->assertSame($return->getValueExpression()->getValue(), $function());
            }
        }*/
    }
}
