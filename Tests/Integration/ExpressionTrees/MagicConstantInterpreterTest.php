<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

trait TestTrait
{
    public function classMagicConstant()
    {
        return __CLASS__;
    }

    public function classMagicConstantInClosure()
    {
        return function () { return __CLASS__; };
    }

    public function traitMagicConstant()
    {
        return __TRAIT__;
    }
}

function testFuncFunctionConstant()
{
    return __FUNCTION__;
}

function testFuncMethodConstant()
{
    return __METHOD__;
}

class MagicConstantInterpreterTest extends InterpreterTest
{
    use TestTrait;

    /**
     * @dataProvider interpreters
     */
    public function testDirectory()
    {
        $this->assertRecompilesCorrectly(function () { return [__DIR__, __dir__]; });
    }

    /**
     * @dataProvider interpreters
     */
    public function testFile()
    {
        $this->assertRecompilesCorrectly(function () { return [__FILE__, __file__]; });
    }

    /**
     * @dataProvider interpreters
     */
    public function testNamespace()
    {
        $this->assertRecompilesCorrectly(function () { return [__NAMESPACE__, __namespace__]; });
    }

    /**
     * @dataProvider interpreters
     */
    public function testClass()
    {
        $this->assertRecompilesCorrectly([$this, 'classMagicConstant']);
        $this->assertRecompilesCorrectly(function () { return [__CLASS__, __class__]; });

        //Apparently when a closure is defined (only) in a trait, __CLASS__ returns the scoped class
        $closure = $this->classMagicConstantInClosure();
        $this->assertRecompilesCorrectly($closure);
    }

    /**
     * @dataProvider interpreters
     */
    public function testTrait()
    {
        $this->assertRecompilesCorrectly(function () { return [__TRAIT__, __trait__]; });
        $this->assertRecompilesCorrectly([$this, 'traitMagicConstant']);
    }

    public function functionConstant()
    {
        return __FUNCTION__;
    }

    /**
     * @dataProvider interpreters
     */
    public function testFunction()
    {
        $this->assertRecompilesCorrectly([$this, 'functionConstant']);
        $this->assertRecompilesCorrectly(function () { return [__FUNCTION__, __function__]; });
        $this->assertRecompilesCorrectly(__NAMESPACE__ . '\\testFuncFunctionConstant');
    }

    public function methodConstant()
    {
        return __METHOD__;
    }

    public function methodConstantInClosure()
    {
        return call_user_func(function () { return __METHOD__; });
    }

    /**
     * @dataProvider interpreters
     */
    // public function testMethod()
    // {
    //     $this->assertRecompilesCorrectly([$this, 'methodConstant']);
    //     $this->assertRecompilesCorrectly(function () { return [__METHOD__, __method__]; });
    //     $this->assertRecompilesCorrectly(__NAMESPACE__ . '\\testFuncMethodConstant');
    //     $this->assertRecompilesCorrectly([$this, 'methodConstantInClosure']);
    // }

    /**
     * @dataProvider interpreters
     */
    public function testLine()
    {
        $this->assertRecompilesCorrectly(function () { return [__LINE__, __line__]; });
    }

    /**
     * @dataProvider interpreters
     */
    public function testParsedFunctionWithMagicConstantsInParameters()
    {
        $this->assertRecompilesCorrectly(function ($i = [__DIR__, __dir__]) { return $i; });
        $this->assertRecompilesCorrectly(function ($i = [__FILE__, __file__]) { return $i; });
        $this->assertRecompilesCorrectly(function ($i = [__NAMESPACE__, __namespace__]) { return $i; });
        $this->assertRecompilesCorrectly(function ($i = [__CLASS__, __class__]) { return $i; });
        $this->assertRecompilesCorrectly(function ($i = [__TRAIT__, __trait__]) { return $i; });
        // $this->assertRecompilesCorrectly(function ($i = [__METHOD__, __method__]) { return $i; });
        $this->assertRecompilesCorrectly(function ($i = [__FUNCTION__, __function__]) { return $i; });
        $this->assertRecompilesCorrectly(function ($i = [__LINE__, __line__]) { return $i; });

        // $this->assertRecompilesCorrectly(function ($i = [
        //     __DiR__         => __FIlE__,
        //     __NAMESPACE__   => __CLass__,
        //     __METHod__      => __LINE__,
        //     __LinE__        => [1,2,3, __tRaIT__]
        // ])
        // { return $i; });
    }
}
