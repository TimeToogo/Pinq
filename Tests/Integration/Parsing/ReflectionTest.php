<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;
use Pinq\Parsing\FunctionInterpreter;
use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Parsing\IFunctionSignature;
use Pinq\Tests\PinqTestCase;

function func($foo = __LINE__)
{

    return ['start' => $foo, 'end' => __LINE__]; }

class ReflectionTest extends PinqTestCase
{
    /**
     * @var IFunctionInterpreter
     */
    protected $interpreter;

    protected function setUp(): void
    {
        $this->interpreter = FunctionInterpreter::getDefault();
    }

    public function testClosure()
    {
        $usedVariable = new \stdClass();
        $usedVariable2 = [[]];
        $startLine  = __LINE__; $closure = function (\stdClass &$i = null, $var = __CLASS__) use ($usedVariable, $usedVariable2) {

        }; $endLine = __LINE__;

        $reflection = $this->interpreter->getReflection($closure);
        $location  = $reflection->getLocation();
        $scope     = $reflection->getScope();
        $signature = $reflection->getSignature();

        $this->assertSame(__FILE__, $location->getFilePath());
        $this->assertSame(true, $location->inNamespace());
        $this->assertSame(__NAMESPACE__, $location->getNamespace());
        $this->assertSame($startLine, $location->getStartLine());
        $this->assertSame($endLine, $location->getEndLine());
        $this->assertSame($this, $scope->getThis());
        $this->assertEquals(['usedVariable' => $usedVariable, 'usedVariable2' => $usedVariable2], $scope->getVariableTable());
        $this->assertSame(__CLASS__, $scope->getScopeType());
        $this->assertSame(__CLASS__, $scope->getThisType());
        $this->assertSame(IFunctionSignature::TYPE_CLOSURE, $signature->getType());
        $this->assertSame(null, $signature->getAccessModifier());
        $this->assertSame(null, $signature->isStatic());
        $this->assertSame(false, $signature->returnsReference());
        $this->assertSame(null, $signature->getName());
        $this->assertSame(null, $signature->getPolymorphModifier());
        $this->assertEquals([
                        O\Expression::parameter('i', '\\stdClass',  O\Expression::value(null), true),
                        O\Expression::parameter('var', null,  O\Expression::value(__CLASS__))],
                $signature->getParameterExpressions());
        $this->assertEquals(['usedVariable', 'usedVariable2'], $signature->getScopedVariableNames());
    }

    final public function & method($start = __LINE__)
    {

        $var = ['start' => $start, 'end' => __LINE__]; return $var; }

    public function testMethod()
    {
        $reflection = $this->interpreter->getReflection([$this, 'method']);
        $location  = $reflection->getLocation();
        $scope     = $reflection->getScope();
        $signature = $reflection->getSignature();

        $this->assertSame(__FILE__, $location->getFilePath());
        $this->assertSame(true, $location->inNamespace());
        $this->assertSame(__NAMESPACE__, $location->getNamespace());
        $this->assertSame($this->method()['start'], $location->getStartLine());
        $this->assertSame($this->method()['end'], $location->getEndLine());
        $this->assertSame($this, $scope->getThis());
        $this->assertEquals([], $scope->getVariableTable());
        $this->assertSame(__CLASS__, $scope->getScopeType());
        $this->assertSame(__CLASS__, $scope->getThisType());
        $this->assertSame(IFunctionSignature::TYPE_METHOD, $signature->getType());
        $this->assertSame(IFunctionSignature::ACCESS_PUBLIC, $signature->getAccessModifier());
        $this->assertSame(false, $signature->isStatic());
        $this->assertSame(true, $signature->returnsReference());
        $this->assertSame('method', $signature->getName());
        $this->assertSame(IFunctionSignature::POLYMORPH_FINAL, $signature->getPolymorphModifier());
        $this->assertEquals([O\Expression::parameter('start', null,  O\Expression::value($this->method()['start']))],
                $signature->getParameterExpressions());
        $this->assertEquals(null, $signature->getScopedVariableNames());
    }

    public static function & staticMethod($start = __LINE__)
    {

        $var = ['start' => $start, 'end' => __LINE__]; return $var; }

    public function testStaticMethod()
    {
        $reflection = $this->interpreter->getReflection([__CLASS__, 'staticMethod']);
        $location  = $reflection->getLocation();
        $scope     = $reflection->getScope();
        $signature = $reflection->getSignature();

        $this->assertSame(__FILE__, $location->getFilePath());
        $this->assertSame(true, $location->inNamespace());
        $this->assertSame(__NAMESPACE__, $location->getNamespace());
        $this->assertSame(self::staticMethod()['start'], $location->getStartLine());
        $this->assertSame(self::staticMethod()['end'], $location->getEndLine());
        $this->assertSame(null, $scope->getThis());
        $this->assertEquals([], $scope->getVariableTable());
        $this->assertSame(__CLASS__, $scope->getScopeType());
        $this->assertSame(null, $scope->getThisType());
        $this->assertSame(IFunctionSignature::TYPE_METHOD, $signature->getType());
        $this->assertSame(IFunctionSignature::ACCESS_PUBLIC, $signature->getAccessModifier());
        $this->assertSame(true, $signature->isStatic());
        $this->assertSame(true, $signature->returnsReference());
        $this->assertSame('staticMethod', $signature->getName());
        $this->assertSame(null, $signature->getPolymorphModifier());
        $this->assertEquals([O\Expression::parameter('start', null,  O\Expression::value(self::staticMethod()['start']))],
                $signature->getParameterExpressions());
        $this->assertEquals(null, $signature->getScopedVariableNames());
    }

    public function testFunction()
    {
        $reflection = $this->interpreter->getReflection(__NAMESPACE__ . '\\func');
        $location  = $reflection->getLocation();
        $scope     = $reflection->getScope();
        $signature = $reflection->getSignature();

        $this->assertSame(__FILE__, $location->getFilePath());
        $this->assertSame(true, $location->inNamespace());
        $this->assertSame(__NAMESPACE__, $location->getNamespace());
        $this->assertSame(func()['start'], $location->getStartLine());
        $this->assertSame(func()['end'], $location->getEndLine());
        $this->assertSame(null, $scope->getThis());
        $this->assertEquals([], $scope->getVariableTable());
        $this->assertSame(null, $scope->getScopeType());
        $this->assertSame(null, $scope->getThisType());
        $this->assertSame(IFunctionSignature::TYPE_FUNCTION, $signature->getType());
        $this->assertSame(null, $signature->getAccessModifier());
        $this->assertSame(null, $signature->isStatic());
        $this->assertSame(false, $signature->returnsReference());
        $this->assertSame(null, $signature->getPolymorphModifier());
        $this->assertSame('func', $signature->getName());
        $this->assertEquals([O\Expression::parameter('foo', null,  O\Expression::value(func()['start']))],
                $signature->getParameterExpressions());
        $this->assertEquals(null, $signature->getScopedVariableNames());
    }

    public function testReflectionSupportsInternalFunctions()
    {
        $reflection = $this->interpreter->getReflection('strlen');

        $this->assertSame('strlen', $reflection->getSignature()->getName());
        $this->assertSame('strlen', $reflection->getInnerReflection()->getName());
        $this->assertSame('strlen', (new \ReflectionFunction($reflection->getCallable()))->getName());
    }

    public function testSameInternalFunctionsProduceSameGlobalHashes()
    {
        $reflection1 = $this->interpreter->getReflection('strlen');
        $reflection2 = $this->interpreter->getReflection('strlen');

        $this->assertSame($reflection1->getGlobalHash(), $reflection2->getGlobalHash());
    }

    public function testInternalFunctionsProduceDifferentGlobalHashes()
    {
        $reflection1 = $this->interpreter->getReflection('strlen');
        $reflection2 = $this->interpreter->getReflection('strpos');

        $this->assertNotSame($reflection1->getGlobalHash(), $reflection2->getGlobalHash());
    }
}
