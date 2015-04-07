<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\INativeType;
use Pinq\Analysis\TolerantExpressionAnalyser;
use Pinq\Expressions as O;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class WeakExpressionAnalysisTest extends ExpressionAnalysisTestCase
{
    protected function setUpExpressionAnalyser()
    {
        return new TolerantExpressionAnalyser($this->typeSystem);
    }

    public function testThatUnknownVariableReturnsMixed()
    {
        $this->assertReturnsNativeType(function () { $var; }, INativeType::TYPE_MIXED);
    }

    public function testThatDynamicExpressionsReturnsMixed()
    {
        $this->assertReturnsNativeType(function () { $class::$field; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function () { $class::method(); }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function () { Foo::$method(); }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function () { Foo::$$field; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function () { $this->$foo; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function () { $this->field; }, INativeType::TYPE_MIXED);
        $this->assertReturnsNativeType(function () { $this->field[434]->{__NON_CONSTANT}->abc()[self::NON_CONSTANT]; }, INativeType::TYPE_MIXED);
    }

    public function testThatAllUnknownExpressionsAreMixedInTheTypeAnalysis()
    {
        $analysis = $this->getAnalysis(function () {
            $this->field[434]->abc();
        });

        /** @var O\VariableExpression $variable */
        $variable = $analysis
            ->getExpression()
            ->getValue()
            ->getValue();

        $this->assertSame(INativeType::TYPE_MIXED, $analysis->getReturnTypeOf($variable)->getIdentifier());
    }
}