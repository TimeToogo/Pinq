<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;
use Pinq\Parsing\InvalidFunctionException;
use Pinq\Parsing\Reflection;
use Pinq\Tests\PinqTestCase;

function funcHelper()
{

}

class ReflectionHelperTest extends PinqTestCase
{
    protected $closureStartLine;
    protected $closureEndLine;

    protected function getClosure()
    {
        $this->closureStartLine = __LINE__; $closure = function () {

        }; $this->closureEndLine = __LINE__;

        return $closure;
    }

    protected function assertIsClosureReflection(\ReflectionFunctionAbstract $reflection)
    {
        $this->getClosure();
        $this->assertInstanceOf('ReflectionFunction', $reflection);
        /** @var $reflection \ReflectionFunction */
        $this->assertTrue($reflection->isClosure());
        $this->assertFalse($reflection->isInternal());
        $this->assertSame(__CLASS__, $reflection->getClosureScopeClass()->getName());
        $this->assertSame($this, $reflection->getClosureThis());
        $this->assertSame(__NAMESPACE__, $reflection->getNamespaceName());
        $this->assertSame('{closure}', $reflection->getShortName());
        $this->assertSame($this->closureStartLine, $reflection->getStartLine());
        $this->assertSame($this->closureEndLine, $reflection->getEndLine());
    }

    public function testClosure()
    {
        $reflection = Reflection::fromCallable($this->getClosure());

        $this->assertIsClosureReflection($reflection);
    }

    public function testClosureAfterReflectionGetClosure()
    {
        $asNewClosure = (new \ReflectionFunction($this->getClosure()))->getClosure()->bindTo($this, __CLASS__);
        $reflection = Reflection::fromCallable($asNewClosure);

        $this->assertIsClosureReflection($reflection);
    }

    public function method()
    {

    }

    protected function assertIsMethodReflection(\ReflectionFunctionAbstract $reflection)
    {
        $this->assertInstanceOf('ReflectionMethod', $reflection);
        /** @var $reflection \ReflectionMethod */
        $this->assertFalse($reflection->isClosure());
        $this->assertFalse($reflection->isInternal());
        $this->assertSame(null, $reflection->getClosureScopeClass());
        $this->assertSame(__CLASS__, $reflection->getDeclaringClass()->getName());
        $this->assertSame(null, $reflection->getClosureThis());
        $this->assertSame('', $reflection->getNamespaceName());
        $this->assertSame('method', $reflection->getShortName());
        $this->assertSame('method', $reflection->getName());
    }

    public function testMethod()
    {
        $reflection = Reflection::fromCallable([$this, 'method']);
        $this->assertIsMethodReflection($reflection);
    }

    public function testMethodAfterReflectionGetClosure()
    {
        $this->expectException(InvalidFunctionException::class);
        $asClosure = (new \ReflectionMethod(__CLASS__, 'method'))->getClosure($this);
        $reflection = Reflection::fromCallable($asClosure);

        $this->assertIsMethodReflection($reflection);
    }

    public static function staticMethod()
    {

    }

    protected function assertIsStaticMethodReflection(\ReflectionFunctionAbstract $reflection)
    {
        $this->assertInstanceOf('ReflectionMethod', $reflection);
        /** @var $reflection \ReflectionMethod */
        $this->assertFalse($reflection->isClosure());
        $this->assertFalse($reflection->isInternal());
        $this->assertSame(null, $reflection->getClosureScopeClass());
        $this->assertSame(__CLASS__, $reflection->getDeclaringClass()->getName());
        $this->assertSame(null, $reflection->getClosureThis());
        $this->assertSame('', $reflection->getNamespaceName());
        $this->assertSame('staticMethod', $reflection->getShortName());
        $this->assertSame('staticMethod', $reflection->getName());
    }

    public function testStaticMethod()
    {
        $reflection = Reflection::fromCallable([$this, 'staticMethod']);
        $this->assertIsStaticMethodReflection($reflection);
    }

    public function testStaticMethodAfterReflectionGetClosure()
    {
        $this->expectException(InvalidFunctionException::class);
        $asClosure = (new \ReflectionMethod(__CLASS__, 'staticMethod'))->getClosure(null);
        $reflection = Reflection::fromCallable($asClosure);

        $this->assertIsStaticMethodReflection($reflection);
    }

    protected function assertIsFunctionReflection(\ReflectionFunctionAbstract $reflection)
    {
        $this->assertInstanceOf('ReflectionFunction', $reflection);
        /** @var $reflection \ReflectionFunction */
        $this->assertFalse($reflection->isClosure());
        $this->assertFalse($reflection->isInternal());
        $this->assertSame(null, $reflection->getClosureScopeClass());
        $this->assertSame(null, $reflection->getClosureThis());
        $this->assertSame(__NAMESPACE__, $reflection->getNamespaceName());
        $this->assertSame('funcHelper', $reflection->getShortName());
        $this->assertSame(__NAMESPACE__ . '\\funcHelper', $reflection->getName());
    }

    public function testFunction()
    {
        $reflection = Reflection::fromCallable(__NAMESPACE__ . '\\funcHelper');

        $this->assertIsFunctionReflection($reflection);
    }

    public function testFunctionAfterReflectionGetClosure()
    {
        $this->expectException(InvalidFunctionException::class);
        $asClosure = (new \ReflectionFunction(__NAMESPACE__ . '\\funcHelper'))->getClosure();
        $reflection = Reflection::fromCallable($asClosure);

        $this->assertIsFunctionReflection($reflection);
    }
}
