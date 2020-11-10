<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\IType;
use Pinq\Expressions as O;
use Pinq\Analysis\INativeType;
use Pinq\Analysis\ITypeAnalysis;
use Pinq\Analysis\TypeException;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TypeAnalysisTest extends ExpressionAnalysisTestCase
{
    protected function doAnalysisTest(callable $expression, callable $test, array $variableTypeMap = [])
    {
        $analysis = $this->getAnalysis($expression, $variableTypeMap, $expression);
        $this->assertSame($this->typeSystem, $analysis->getTypeSystem());
        $test($analysis, $expression);
    }

    protected function assertCorrectType(ITypeAnalysis $analysis, $type, O\Expression $expression)
    {
        $this->assertEqualTypes($type, $analysis->getReturnTypeOf($expression));
    }

    protected function assertTypeMatchesValue(ITypeAnalysis $analysis, O\Expression $expression, IType $metadataType = null)
    {
        $type = $this->typeSystem->getTypeFromValue($expression->evaluate(O\EvaluationContext::staticContext(__NAMESPACE__, __CLASS__)));
        $this->assertEqualTypes($type, $analysis->getReturnTypeOf($expression));
        if($metadataType !== null) {
            $this->assertEqualTypes($metadataType, $type, $expression->compileDebug());
        }
    }

    public function testNativeTypes()
    {
        $values = [
                INativeType::TYPE_STRING,
                INativeType::TYPE_INT,
                INativeType::TYPE_BOOL,
                INativeType::TYPE_DOUBLE,
                INativeType::TYPE_NULL,
                INativeType::TYPE_ARRAY,
                INativeType::TYPE_RESOURCE,
        ];

        foreach($values as $expectedType) {
            $this->doAnalysisTest(
                    function () { $var; },
                    function (ITypeAnalysis $analysis, O\VariableExpression $expression) use ($expectedType) {
                        $this->assertCorrectType($analysis,
                                $this->typeSystem->getNativeType($expectedType),
                                $expression);
                    },
                    ['var' => $this->typeSystem->getNativeType($expectedType)]
            );
        }
    }

    public function testCasts()
    {
        $values = [
                INativeType::TYPE_STRING => function () { (string)'abc'; },
                INativeType::TYPE_INT => function () { (int)'abc'; },
                INativeType::TYPE_BOOL => function () { (bool)1; },
                INativeType::TYPE_DOUBLE => function () { (double)false; },
                INativeType::TYPE_ARRAY => function () { (array)'abc'; },
        ];

        foreach($values as $expectedType => $expression) {
            $this->doAnalysisTest($expression,
                    function (ITypeAnalysis $analysis, O\CastExpression $expression) use ($expectedType) {
                        $this->assertTypeMatchesValue(
                                $analysis,
                                $expression->getCastValue(),
                                $analysis->getCast($expression)->getSourceType());
                        $this->assertEqualsNativeType(
                                $expectedType,
                                $analysis->getCast($expression)->getReturnType());
                    }
            );
        }
    }

    public function testUnaryOperators()
    {
        $values = [
                INativeType::TYPE_INT => function () { +4; },
                INativeType::TYPE_BOOL => function () { !true; },
                INativeType::TYPE_DOUBLE => function () { -343.23; },
                INativeType::TYPE_STRING => function () { ~'abce'; },
        ];

        foreach($values as $expectedType => $expression) {
            $this->doAnalysisTest($expression,
                    function (ITypeAnalysis $analysis, O\UnaryOperationExpression $expression) use ($expectedType) {
                        $this->assertTypeMatchesValue(
                                $analysis,
                                $expression->getOperand(),
                                $analysis->getUnaryOperation($expression)->getSourceType());
                        $this->assertEqualsNativeType(
                                $expectedType,
                                $analysis->getUnaryOperation($expression)->getReturnType());
                    }
            );
        }
    }

    public function testFunctionCalls()
    {
        $this->doAnalysisTest(
                function () { strlen('abc'); },
                function (ITypeAnalysis $analysis, O\FunctionCallExpression $expression) {
                    $this->assertEqualsNativeType(
                            INativeType::TYPE_INT,
                            $analysis->getFunction($expression)->getReturnType());
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getArguments()[0]->getValue());
                    $this->assertSame('strlen', $analysis->getFunction($expression)->getName());
                    $this->assertSame('strlen', $analysis->getFunction($expression)->getReflection()->getName());
                }
        );
    }

    public function testStaticMethodCall()
    {
        $this->doAnalysisTest(
                function () { \DateTime::createFromFormat('U', 1993); },
                function (ITypeAnalysis $analysis, O\StaticMethodCallExpression $expression) {
                    $this->assertEqualsObjectType(
                            'DateTime',
                            $analysis->getStaticMethod($expression)->getReturnType());
                    $this->assertEqualsObjectType('DateTime', $analysis->getStaticMethod($expression)->getSourceType());
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getArguments()[0]->getValue());
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getArguments()[1]->getValue());
                    $this->assertSame('createFromFormat', $analysis->getStaticMethod($expression)->getName());
                    $this->assertSame('createFromFormat', $analysis->getStaticMethod($expression)->getReflection()->getName());
                }
        );
    }

    protected static $foo;

    public function testStaticField()
    {
        $this->doAnalysisTest(
                function () { self::$foo; },
                function (ITypeAnalysis $analysis, O\StaticFieldExpression $expression) {
                    $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $analysis->getReturnTypeOf($expression));
                    $this->assertEqualsNativeType(
                            INativeType::TYPE_MIXED,
                            $analysis->getStaticField($expression)->getReturnType()
                    );
                    $this->assertEqualsObjectType(__CLASS__, $analysis->getStaticField($expression)->getSourceType());
                    $this->assertSame('foo', $analysis->getStaticField($expression)->getName());
                    $this->assertSame(true, $analysis->getStaticField($expression)->isStatic());
                }
        );
    }

    public function testNew()
    {
        $this->doAnalysisTest(
                function () { new \DateTimeZone('123'); },
                function (ITypeAnalysis $analysis, O\NewExpression $expression) {
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getArguments()[0]->getValue(),
                            $this->typeSystem->getNativeType(INativeType::TYPE_STRING));
                    $this->assertEqualsObjectType('DateTimeZone', $analysis->getConstructor($expression)->getSourceType());
                    $this->assertEqualsObjectType('DateTimeZone', $analysis->getConstructor($expression)->getReturnType());
                    $this->assertSame(true, $analysis->getConstructor($expression)->getReflection()->isConstructor());
                    $this->assertSame('DateTimeZone', $analysis->getConstructor($expression)->getReflection()->getDeclaringClass()->getName());
                }
        );
    }

    public function testMethodCalls()
    {
        $this->doAnalysisTest(
                function (\DateTime $dateTime) { $dateTime->format('abc'); },
                function (ITypeAnalysis $analysis, O\MethodCallExpression $expression) {
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getArguments()[0]->getValue());
                    $this->assertEqualsObjectType('DateTime', $analysis->getMethod($expression)->getSourceType());
                    $this->assertEqualsNativeType(INativeType::TYPE_STRING, $analysis->getMethod($expression)->getReturnType());
                    $this->assertSame('format', $analysis->getMethod($expression)->getReflection()->getName());
                    $this->assertSame('format', $analysis->getMethod($expression)->getName());
                }
        );
    }

    public function testInvocation()
    {
        $this->doAnalysisTest(
                function (\Closure $closure) { $closure('abc'); },
                function (ITypeAnalysis $analysis, O\InvocationExpression $expression) {
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getArguments()[0]->getValue(),
                            $this->typeSystem->getNativeType(INativeType::TYPE_STRING));
                    $this->assertEqualsObjectType('Closure', $analysis->getInvocation($expression)->getSourceType());
                    $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $analysis->getInvocation($expression)->getReturnType());
                }
        );
        $this->assertReturnsNativeType(function (\Closure $closure) { $closure(); }, INativeType::TYPE_MIXED);
    }

    public function testFields()
    {
        $this->doAnalysisTest(
                function (\DateInterval $interval) { $interval->d; },
                function (ITypeAnalysis $analysis, O\FieldExpression $expression) {
                    $this->assertEqualsObjectType('DateInterval', $analysis->getField($expression)->getSourceType());
                    $this->assertEqualsNativeType(INativeType::TYPE_INT, $analysis->getField($expression)->getReturnType());
                    $this->assertSame('d', $analysis->getField($expression)->getName());
                    $this->assertSame(false, $analysis->getField($expression)->isStatic());
                }
        );
    }

    public function testIndexers()
    {
        $this->doAnalysisTest(
                function (array $array) { $array['abc']; },
                function (ITypeAnalysis $analysis, O\IndexExpression $expression) {
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getIndex());
                    $this->assertEqualsNativeType(INativeType::TYPE_ARRAY, $analysis->getIndex($expression)->getSourceType());
                    $this->assertEqualsNativeType(INativeType::TYPE_MIXED, $analysis->getIndex($expression)->getReturnType());
                }
        );
    }

    public function testIsset()
    {
        $this->doAnalysisTest(
                function () { isset(self::$foo); },
                function (ITypeAnalysis $analysis, O\IssetExpression $expression) {
                    $this->assertEqualsNativeType(
                            INativeType::TYPE_MIXED,
                            $analysis->getReturnTypeOf($expression->getValues()[0])
                    );
                    $this->assertEqualsNativeType(INativeType::TYPE_BOOL, $analysis->getReturnTypeOf($expression));
                }
        );
    }

    public function testEmpty()
    {
        $this->doAnalysisTest(
                function () { empty(self::$foo); },
                function (ITypeAnalysis $analysis, O\EmptyExpression $expression) {
                    $this->assertEqualsNativeType(
                            INativeType::TYPE_MIXED,
                            $analysis->getReturnTypeOf($expression->getValue())
                    );
                    $this->assertEqualsNativeType(INativeType::TYPE_BOOL, $analysis->getReturnTypeOf($expression));
                }
        );
    }

    public function testClosure()
    {
        $this->doAnalysisTest(
                function (\stdClass $foo) { function (array $bar) use ($foo) { $foo; $bar;}; },
                function (ITypeAnalysis $analysis, O\ClosureExpression $expression) {
                    $this->assertEqualsObjectType(
                            'stdClass',
                            $analysis->getReturnTypeOf($expression->getBodyExpressions()[0]));
                    $this->assertEqualsNativeType(
                            INativeType::TYPE_ARRAY,
                            $analysis->getReturnTypeOf($expression->getBodyExpressions()[1]));
                    $this->assertEqualsObjectType('Closure', $analysis->getReturnTypeOf($expression));
                }
        );
    }

    public function testTernary()
    {
        $values = [
                INativeType::TYPE_INT => function () { 3.2 ? 1 : -56; },
                INativeType::TYPE_BOOL => function () { 'abc' ? true : false; },
                INativeType::TYPE_DOUBLE => function () { 3 ? 343.23 : 2.34; },
                INativeType::TYPE_STRING => function () { false ? 'abce' : '1234.3'; },
                INativeType::TYPE_ARRAY => function () { false ? ['abc'] : [1,2,3]; },
                INativeType::TYPE_NUMERIC => function () { '' ? 3 : 4.3; },
                INativeType::TYPE_MIXED => function () { '' ? '3' : 4.3; },
        ];

        foreach($values as $expectedType => $expression) {
            $this->doAnalysisTest($expression,
                    function (ITypeAnalysis $analysis, O\TernaryExpression $expression) use ($expectedType) {
                        $this->assertTypeMatchesValue(
                                $analysis,
                                $expression->getIfFalse()
                        );
                        $this->assertTypeMatchesValue(
                                $analysis,
                                $expression->getIfTrue()
                        );
                        $this->assertEqualsNativeType(
                                $expectedType,
                                $analysis->getReturnTypeOf($expression)
                        );
                    }
            );
        }
    }

    public function testBinaryOperators()
    {
        $values = [
                INativeType::TYPE_INT => function () { 2 + 4; },
                INativeType::TYPE_BOOL => function () { true && false; },
                INativeType::TYPE_DOUBLE => function () { 343.23 * 2.34; },
                INativeType::TYPE_STRING => function () { 'abce' . 1234.3; },
                INativeType::TYPE_ARRAY => function () { ['abc'] + [1,2,3]; },
        ];

        foreach($values as $expectedType => $expression) {
            $this->doAnalysisTest($expression,
                    function (ITypeAnalysis $analysis, O\BinaryOperationExpression $expression) use ($expectedType) {
                        $this->assertTypeMatchesValue(
                                $analysis,
                                $expression->getLeftOperand()
                        );
                        $this->assertTypeMatchesValue(
                                $analysis,
                                $expression->getRightOperand()
                        );

                        $this->assertSame($expression->getOperator(), $analysis->getBinaryOperation($expression)->getOperator());
                    }
            );
        }
    }

    public function testThrow()
    {
        $this->doAnalysisTest(
                function () { throw new \LogicException(); },
                function (ITypeAnalysis $analysis, O\ThrowExpression $expression) {
                    $this->assertTypeMatchesValue(
                            $analysis,
                            $expression->getException(),
                            $this->typeSystem->getObjectType('LogicException'));
                }
        );
    }

    public function testReturnTypeWithInvalidExpressionThrowsException()
    {
        $this->expectException(TypeException::class);
        //Data stored through identity
        $this->getAnalysis(function () { 1; })->getReturnTypeOf(O\Expression::value(1));
    }

    public function testMetaDataWithInvalidExpressionThrowsException()
    {
        $this->expectException(TypeException::class);
        $this->getAnalysis(function () { 1 + 2; })->getFunction(O\Expression::functionCall(O\Expression::value('s')));
    }

}