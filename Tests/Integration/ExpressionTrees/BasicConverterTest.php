<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use \Pinq\Expressions as O;

class BasicConverterTest extends ConverterTest
{
    /**
     * @dataProvider Converters
     */
    public function testEmptyFunction()
    {
        $this->AssertConvertsAndRecompilesCorrectly(function () { }, []);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testConstantReturnValueFunctions()
    {
        $this->AssertConvertsAndRecompilesCorrectly(function () { return 1; }, [], O\Expression::Value(1));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return 2; }, [], O\Expression::Value(2));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return '1'; }, [], O\Expression::Value('1'));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return 1.01; }, [], O\Expression::Value(1.01));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return true; }, [], O\Expression::Value(true));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return null; }, [], O\Expression::Value(null));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return [1,5,57,4 => 3, 'tset' => 'ftest']; }, [], 
                O\Expression::ArrayExpression(
                        [null, null, null, O\Expression::Value(4), O\Expression::Value('tset')], 
                        [O\Expression::Value(1), O\Expression::Value(5), O\Expression::Value(57), O\Expression::Value(3), O\Expression::Value('ftest')]));
        
        $this->AssertConvertsAndRecompilesCorrectly(function () { return new \stdClass(); }, [], O\Expression::NewExpression(O\Expression::Value('stdClass')));
    }
    
    /**
     * @dataProvider Converters
     */
    public function testBinaryOperations()
    {
        $ValueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I + 1; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I - 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I / 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I * 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I % 5; }, $ValueSet);
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I & 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I | 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I << 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I >> 5; }, $ValueSet);
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I && 0; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I || 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I == 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I === 1; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I != 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I != 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I > 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I >= 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I < 5; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I <= 5; }, $ValueSet);
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I . 5; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testUnaryOperations()
    {
        $ValueSet = [[1], [0]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return -$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return !$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return ~$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return +$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I++; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I--; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return ++$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return --$I; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testCastOperations()
    {
        $ValueSet = [[1], [0], [true], [false]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return (string)$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return (bool)$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return (int)$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return (array)$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return (object)$I; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return (double)$I; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testLanguageConstructs()
    {
        $ValueSet = [[''], ['1'], ['test'], [null], [true], [false], [['dsffd']], [[]], [0]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return isset($I); }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return empty($I); }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testTernary()
    {
        $ValueSet = [[1], [0], [-1], [-5], [5], [-500], [500]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I > 0 ? 5 : -50; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testObjectMethodCall()
    {
        $ValueSet = [[new \DateTime()], [new \DateTime('-3 days')], [new \DateTime('+2 weeks')]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function (\DateTime $I) { return $I->format(\DateTime::ATOM); }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function (\DateTime $I) { return $I->getTimestamp(); }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testObjectField()
    {
        $ValueSet = [[(object)['prop' => null, 'foo' => null]], [(object)['prop' => true, 'foo' => false]], [(object)['prop' => 'fdsfdsdf', 'foo' => 'fewfew']]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function (\stdClass $I) { return $I->prop; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function (\stdClass $I) { return $I->foo; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testValueIndex()
    {
        $ValueSet = [[['prop' => null, 'foo' => null]], [['prop' => true, 'foo' => false]], [['prop' => 'fdsfdsdf', 'foo' => 'fewfew']]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function (array $I) { return $I['prop']; }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function (array $I) { return $I['foo']; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testFunctionCall()
    {
        $ValueSet = [[''], ['1'], ['test'], ['fooo'], ['geges ges  gse e'], ['striiiiiiiiiiing']];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return strlen($I); }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return str_split($I); }, $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return explode(' ', $I); }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testInternalFunction()
    {
        $ValueSet = [[''], ['1'], ['test'], ['fooo'], ['geges ges  gse e'], ['striiiiiiiiiiing']];
        
        $this->AssertConvertsAndRecompilesCorrectly('strlen', $ValueSet);
        $this->AssertConvertsAndRecompilesCorrectly('str_split', $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testStaticMethodCall()
    {
        $ValueSet = [[range(0, 10)], [range(-100, 100)], [[1, '2', 3, '4', 5, '6', 7, '8', 9, '0']]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function (array $I) { return \SplFixedArray::fromArray($I); }, $ValueSet);
    }
}
