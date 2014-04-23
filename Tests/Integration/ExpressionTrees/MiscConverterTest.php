<?php 

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

class MiscConverterTest extends ConverterTest
{
    /**
     * @dataProvider Converters
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
     * @dataProvider Converters
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
                    function ($i) use($nonExistent) {
                        
                    };
                },
                ['nonExistent']);
    }
    
    /**
     * @dataProvider Converters
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
}