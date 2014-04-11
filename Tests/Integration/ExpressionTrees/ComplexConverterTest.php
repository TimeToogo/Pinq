<?php

namespace Pinq\Tests\Integration\ExpressionTrees;


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