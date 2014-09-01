<?php

namespace Pinq\Tests\Integration\Parsing;

use Pinq\Expressions as O;

class ConstantsTest extends ParserTest
{
    /**
     * @dataProvider parsers
     */
    public function testNormalConstant()
    {
        $this->assertParsedAs(
                function () {
                    I_AM_CONSTANT;
                    I_AM_ANOTHER_CONSTANT;
                    namespace\TEST_CONST;
                },
                [O\Expression::constant('I_AM_CONSTANT'),
                O\Expression::constant('I_AM_ANOTHER_CONSTANT'),
                O\Expression::constant('\\' . __NAMESPACE__ . '\\TEST_CONST')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testClassConstant()
    {
        $this->assertParsedAs(
                function () {
                    I_AM::A_CLASS_CONSTANT;
                    BOO::YEA;
                },
                [O\Expression::classConstant(O\Expression::value('\\' . __NAMESPACE__ . '\\I_AM'), 'A_CLASS_CONSTANT'),
                O\Expression::classConstant(O\Expression::value('\\' . __NAMESPACE__ . '\\BOO'), 'YEA')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicDir()
    {
        $this->assertParsedAs(
                function () {
                    __DIR__;
                },
                [O\Expression::constant('__DIR__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicFile()
    {
        $this->assertParsedAs(
                function () {
                    __FILE__;
                },
                [O\Expression::constant('__FILE__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicNamespace()
    {
        $this->assertParsedAs(
                function () {
                    __NAMESPACE__;
                },
                [O\Expression::constant('__NAMESPACE__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicClass()
    {
        $this->assertParsedAs(
                function () {
                    __CLASS__;
                },
                [O\Expression::constant('__CLASS__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicTrait()
    {
        $this->assertParsedAs(
                function () {
                    __TRAIT__;
                },
                [O\Expression::constant('__TRAIT__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicFunction()
    {
        $this->assertParsedAs(
                function () {
                    __FUNCTION__;
                },
                [O\Expression::constant('__FUNCTION__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicMethod()
    {
        $this->assertParsedAs(
                function () {
                    __METHOD__;
                },
                [O\Expression::constant('__METHOD__')]);
    }

    /**
     * @dataProvider parsers
     */
    public function testMagicLine()
    {
        $this->assertParsedAs(
                function () {
                    __LINE__;
                },
                [O\Expression::value(__LINE__ - 2)]);
    }
}
