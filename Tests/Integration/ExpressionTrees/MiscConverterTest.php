<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use \Pinq\Expressions as O;

class MiscConverterTest extends ConverterTest
{
    /**
     * @dataProvider Converters
     */
    public function testParameterExpressions()
    {
        $this->AssertParametersAre(function ($I) {  }, [O\Expression::Parameter('I')]);
        $this->AssertParametersAre(function ($I, $Foo) {  }, [O\Expression::Parameter('I'), O\Expression::Parameter('Foo')]);
        $this->AssertParametersAre(function ($I = null) {  }, [O\Expression::Parameter('I', null, true, null)]);
        $this->AssertParametersAre(function ($I = 'bar') {  }, [O\Expression::Parameter('I', null, true, 'bar')]);
        $this->AssertParametersAre(function (\DateTime $I) {  }, [O\Expression::Parameter('I', 'DateTime')]);
        $this->AssertParametersAre(function (&$I) {  }, [O\Expression::Parameter('I', null, false, null, true)]);
        $this->AssertParametersAre(function (\stdClass &$I = null, array $Array =  ['foo']) {  }, [O\Expression::Parameter('I', 'stdClass', true, null, true), O\Expression::Parameter('Array', 'array', true,  ['foo'])]);
        $this->AssertParametersAre(function (callable &$V = null) {  }, [O\Expression::Parameter('V', 'callable', true, null, true)]);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testUnresolvedVariables()
    {
        $this->AssertUnresolvedVariablesAre(function () { return null; }, []);
        $this->AssertUnresolvedVariablesAre(function ($I) { return $I; }, []);
        $this->AssertUnresolvedVariablesAre(function ($I) { return $I . $I; }, []);
        $this->AssertUnresolvedVariablesAre(function ($I) { return $I . $Foo; }, ['Foo']);
        $this->AssertUnresolvedVariablesAre(function () { return $I . $Foo; }, ['I', 'Foo']);
        $this->AssertUnresolvedVariablesAre(function () { return $I . $I; }, ['I']);
        $this->AssertUnresolvedVariablesAre(function () { $Boo = $Nah; }, ['Nah']);
        $this->AssertUnresolvedVariablesAre(function () { $Boo += $Nah; }, ['Boo', 'Nah']);
        $this->AssertUnresolvedVariablesAre(function () { $$Boo; }, ['$Boo']);
        $this->AssertUnresolvedVariablesAre(function () { function ($I) use ($NonExistent) {}; }, ['NonExistent']);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testUnresolvedVariableInNestedClosure()
    {
        $this->AssertUnresolvedVariablesAre(function () { return function ($I) { return $I; }; }, []);
        $this->AssertUnresolvedVariablesAre(function () { return function ($I) { return $Bar; }; }, ['Bar']);
    }
}
