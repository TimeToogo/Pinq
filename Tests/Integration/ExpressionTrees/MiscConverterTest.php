<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

class MiscConverterTest extends ConverterTest
{
    /**
     * @dataProvider converters
     */
    public function testParameterExpressions()
    {
        $this->assertParametersAre(
                function ($i) { },
                [O\Expression::parameter('i')]);

        $this->assertParametersAre(
                function ($i, $foo) { },
                [O\Expression::parameter('i'), O\Expression::parameter('foo')]);

        $this->assertParametersAre(
                function ($i = null) { },
                [O\Expression::parameter('i', null, true, null)]);

        $this->assertParametersAre(
                function ($i = 'bar') { },
                [O\Expression::parameter('i', null, true, 'bar')]);

        $this->assertParametersAre(
                function (\DateTime $i) { },
                [O\Expression::parameter('i', 'DateTime')]);

        $this->assertParametersAre(
                function (&$i) { },
                [O\Expression::parameter('i', null, false, null, true)]);

        $this->assertParametersAre(
                function (\stdClass &$i = null, array $array = ['foo']) { },
                [O\Expression::parameter('i', 'stdClass', true, null, true), O\Expression::parameter('array', 'array', true, ['foo'])]);

        $this->assertParametersAre(
                function (callable &$v = null) { },
                [O\Expression::parameter('v', 'callable', true, null, true)]);
    }

    /**
     * @dataProvider converters
     */
    public function testUnresolvedVariables()
    {
        $this->assertUnresolvedVariablesAre(function () { return null; }, []);
        $this->assertUnresolvedVariablesAre(function ($i) { return $i; }, []);
        $this->assertUnresolvedVariablesAre(function ($i) { return $i . $i; }, []);
        $this->assertUnresolvedVariablesAre(function ($i) { return $i . $foo; }, ['foo']);
        $this->assertUnresolvedVariablesAre(function () { return $i . $foo; }, ['i', 'foo']);
        $this->assertUnresolvedVariablesAre(function () { return $i . $i; }, ['i']);
        $this->assertUnresolvedVariablesAre(function () { $boo = $nah; }, ['nah']);
        $this->assertUnresolvedVariablesAre(function () { $boo += $nah; }, ['boo', 'nah']);
        $this->assertUnresolvedVariablesAre(function () { ${$boo}; }, ['$boo']);

        $this->assertUnresolvedVariablesAre(
                function () {
                    function ($i) use ($nonExistent) {

                    };
                },
                ['nonExistent']);
    }

    /**
     * @dataProvider converters
     */
    public function testUnresolvedVariableInNestedClosure()
    {
        $this->assertUnresolvedVariablesAre(
                function () {
                    return function ($i) {
                        return $i;
                    };
                },
                []);

        $this->assertUnresolvedVariablesAre(
                function () {
                    return function ($i) {
                        return $bar;
                    };
                },
                ['bar']);
    }

    /**
     * @dataProvider converters
     */
    public function testInternalFunction()
    {
        $valueSet = [
            [''],
            ['1'],
            ['test'],
            ['fooo'],
            ['geges ges  gse e'],
            ['striiiiiiiiiiing']
        ];

        $this->assertConvertsAndRecompilesCorrectly('strlen', $valueSet);
        $this->assertConvertsAndRecompilesCorrectly('str_split', $valueSet);
    }

    /**
     * @dataProvider converters
     */
    public function testVariadicInternalFunction()
    {
        $valueSet = [
            [[1], [2], [3]], 
            [[1, 3], [2, 5], [6, 3]],
            [['test' => 5], ['foo' => 'bar'], ['baz' => 'boron']],
        ];

        $this->assertConvertsAndRecompilesCorrectly('array_merge', $valueSet);
    }
}
