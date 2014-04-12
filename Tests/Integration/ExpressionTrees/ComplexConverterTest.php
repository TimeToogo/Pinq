<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use \Pinq\Expressions as O;

class ComplexConverterTest extends ConverterTest
{
    /**
     * @dataProvider Converters
     */
    public function testDefaultOrderOfBinaryOperationFunction()
    {
        $ValueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) { return $I * -$I + 5 ^ $I / -$I % $I + 2 << $I - +$I;  }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testUsedVariableResolution()
    {
        $ValueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        
        $Factor = 5;
        $this->AssertConvertsAndRecompilesCorrectly(function ($I) use($Factor) { return $I * $Factor; }, $ValueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testVariableReturnValueResolution()
    {
        foreach([1, null, 'test', false, new \stdClass(), [1234567890, 'tests', new \stdClass()]] as $Value) {
            $this->AssertFirstResolvedReturnExpression(function () use($Value) { return $Value; }, O\Expression::Value($Value));
        }
    }
    
    /**
     * @dataProvider Converters
     */
    public function testOperationsReturnValueResolution()
    {
        $Value = 5;
        $this->AssertReturnValueResolvesCorrectly(function () use($Value) { return $Value + 1; });
        $this->AssertReturnValueResolvesCorrectly(function () use($Value) { return $Value - 1; });
        $this->AssertReturnValueResolvesCorrectly(function () use($Value) { return $Value * 5; });
        $this->AssertReturnValueResolvesCorrectly(function () use($Value) { return $Value / $Value + $Value - $Value % ~$Value; });
    }
    
    /**
     * @dataProvider Converters
     */
    public function testComplexReturnValueResolution()
    {
        $Value = 5;
        $this->AssertReturnValueResolvesCorrectly(
                function () use($Value) {
                    $Copy = $Value;
                    return $Copy + 1; 
                });
                
        $this->AssertReturnValueResolvesCorrectly(
                function () use($Value) {
                    $Copy =& $Value;
                    $Another = $Value + 1;
                    return $Copy + 1 - $Another; 
                });
                
        $this->AssertReturnValueResolvesCorrectly(
                function () use($Value) {
                    $Copy = $Value / $Value + $Value - $Value % ~$Value;
                    $Another = $Value % 34 - $Copy + $Value;
                    return $Another - $Copy + 1 - $Another * $Value % 34; 
                });
    }
    /**
     * @dataProvider Converters
     */
    public function testComplexReturnValueResolutionWithVariableVariables()
    {
        $Value = 5;
        $Variable = 'Value';
        $this->AssertReturnValueResolvesCorrectly(
                function () use($Value, $Variable) {
                    $Copy = $$Variable;
                    return $Copy + 1; 
                });
                
        $this->AssertReturnValueResolvesCorrectly(
                function () use($Value, $Variable) {
                    $Copy =& $$Variable;
                    $Another = $Value + 1;
                    return $Copy + 1 - $Another; 
                });
                
        $this->AssertReturnValueResolvesCorrectly(
                function () use($Value, $Variable) {
                    $Copy = $$Variable / $Value + $Value - $Value % ~$$Variable;
                    $Another = $Value % 34 - $Copy + $$Variable;
                    return $Another - $Copy + 1 - $Another * $Value % 34; 
                });
    }
    
    final protected function AssertReturnValueResolvesCorrectly(callable $Function) 
    {
        $this->AssertFirstResolvedReturnExpression($Function, O\Expression::Value($Function()));
    }
    
    /** ---- Some code from the wild ---- **/
    
    /**
     * @dataProvider Converters
     * @source http://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
     */
    public function testFileSizeFormatter()
    {
        $Formatter = function($bytes, $precision = 2) 
        { 
            $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

            $bytes = max($bytes, 0); 
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
            $pow = min($pow, count($units) - 1); 

            // Uncomment one of the following alternatives
            // $bytes /= pow(1024, $pow);
            // $bytes /= (1 << (10 * $pow)); 

            return round($bytes, $precision) . ' ' . $units[$pow]; 
        };
        
        $ValueSet = [[0], [1000], [500050], [323241234], [5000000]];
        
        $this->AssertConvertsAndRecompilesCorrectly($Formatter, $ValueSet);
    }
}