<?php 

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;
use Pinq\Queries;

class ComplexConverterTest extends ConverterTest
{
    /**
     * @dataProvider Converters
     */
    public function testDefaultOrderOfBinaryOperationFunction()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        
        $this->assertConvertsAndRecompilesCorrectly(
                function ($i) {
                    return $i * -$i + 5 ^ $i / -$i % $i + 2 << $i - +$i;
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testUsedVariableResolution()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        $factor = 5;
        
        $this->assertConvertsAndRecompilesCorrectly(
                function ($i) use($factor) {
                    return $i * $factor;
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testNestedClosureUsedVariableResolution()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        $factor = 5;
        
        $this->assertConvertsAndRecompilesCorrectly(
                function ($i) use($factor) {
                    $innerClosue = 
                            function ($i) use($factor) {
                                return $i * $factor;
                            };
                    
                    return $innerClosue($i);
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testVariableReturnValueResolution()
    {
        foreach([1, null, 'test', false, new \stdClass(), [1234567890, 'tests', new \stdClass(), function () {}]] as $value) {
            $this->assertFirstResolvedReturnExpression(
                    function () use($value) {
                        return $value;
                    },
                    O\Expression::value($value));
        }
    }
    
    /**
     * @dataProvider Converters
     */
    public function testNestedClosure()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2], [5], [500]];
        
        $this->assertConvertsAndRecompilesCorrectly(
                function ($i) {
                    $divider = 
                            function () use($i) {
                                return $i / 5;
                            };
                    
                    return $divider();
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testOperationsReturnValueResolution()
    {
        $value = 5;
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            return $value + 1;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            return $value - 1;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            return $value * 5;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            return $value / $value + $value - $value % ~$value;
        });
    }
    
    /**
     * @dataProvider Converters
     */
    public function testComplexReturnValueResolution()
    {
        $value = 5;
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            $copy = $value;
            
            return $copy + 1;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            $copy =& $value;
            $another = $value + 1;
            
            return $copy + 1 - $another;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value) {
            $copy = $value / $value + $value - $value % ~$value;
            $another = $value % 34 - $copy + $value;
            
            return $another - $copy + 1 - $another * $value % 34;
        });
    }
    
    /**
     * @dataProvider Converters
     */
    public function testComplexReturnValueResolutionWithVariableVariables()
    {
        $value = 5;
        $variable = 'value';
        
        $this->assertReturnValueResolvesCorrectly(function () use($value, $variable) {
            $copy = ${$variable};
            
            return $copy + 1;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value, $variable) {
            $copy =& ${$variable};
            $another = $value + 1;
            
            return $copy + 1 - $another;
        });
        
        $this->assertReturnValueResolvesCorrectly(function () use($value, $variable) {
            $copy = ${$variable} / $value + $value - $value % ~${$variable};
            $another = $value % 34 - $copy + ${$variable};
            
            return $another - $copy + 1 - $another * $value % 34;
        });
    }
    
    protected final function assertReturnValueResolvesCorrectly(callable $function)
    {
        $this->assertFirstResolvedReturnExpression(
                $function,
                O\Expression::value($function()));
    }
    
    /**
     * @dataProvider Converters
     */
    public function testThatResolvesSubQueries()
    {
        $this->assertFirstResolvedReturnExpression(
                function (\Pinq\ITraversable $traversable) {
                    return $traversable->asArray();
                },
                O\Expression::subQuery(
                        O\Expression::variable(O\Expression::value('traversable')),
                        new Queries\RequestQuery(
                                new Queries\Scope([]),
                                new Queries\Requests\Values()),
                        O\Expression::methodCall(
                                O\Expression::variable(O\Expression::value('traversable')),
                                O\Expression::value('asArray'))));
                
                
        $this->assertFirstResolvedReturnExpression(
                function (\Pinq\ITraversable $traversable) {
                    return $traversable
                            ->where(function ($i) { return $i > 0; })
                            ->all(function ($i) { return $i % 2 === 0; });
                },
                O\Expression::subQuery(
                        O\Expression::variable(O\Expression::value('traversable')),
                        new Queries\RequestQuery(
                                new Queries\Scope([new Queries\Segments\Filter(new \Pinq\FunctionExpressionTree(
                                        null,
                                        [O\Expression::parameter('i')],
                                        [O\Expression::returnExpression(O\Expression::binaryOperation(
                                                O\Expression::variable(O\Expression::value('i')),
                                                O\Operators\Binary::GREATER_THAN,
                                                O\Expression::value(0)))]))]),
                                new Queries\Requests\All(new \Pinq\FunctionExpressionTree(
                                        null,
                                        [O\Expression::parameter('i')],
                                        [O\Expression::returnExpression(O\Expression::binaryOperation(
                                                O\Expression::binaryOperation(
                                                        O\Expression::variable(O\Expression::value('i')),
                                                        O\Operators\Binary::MODULUS,
                                                        O\Expression::value(2)),
                                                O\Operators\Binary::IDENTITY,
                                                O\Expression::value(0)))]))),
                        O\Expression::methodCall(
                                O\Expression::methodCall(
                                        O\Expression::variable(O\Expression::value('traversable')),
                                        O\Expression::value('where'),
                                        [O\Expression::closure(
                                                [O\Expression::parameter('i')],
                                                [],
                                                [O\Expression::returnExpression(O\Expression::binaryOperation(
                                                        O\Expression::variable(O\Expression::value('i')),
                                                        O\Operators\Binary::GREATER_THAN,
                                                        O\Expression::value(0)))])]),
                                O\Expression::value('all'),
                                [O\Expression::closure(
                                        [O\Expression::parameter('i')],
                                        [],
                                        [O\Expression::returnExpression(O\Expression::binaryOperation(
                                                O\Expression::binaryOperation(
                                                        O\Expression::variable(O\Expression::value('i')),
                                                        O\Operators\Binary::MODULUS,
                                                        O\Expression::value(2)),
                                                O\Operators\Binary::IDENTITY,
                                                O\Expression::value(0)))])])));
    }
    
    /** ---- Some code from the wild ---- **/
    /**
     * @dataProvider Converters
     * @source http://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
     */
    public function testFileSizeFormatter()
    {
        $formatter = function ($bytes, $precision = 2) { 
            $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

            $bytes = max($bytes, 0); 
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
            $pow = min($pow, count($units) - 1); 

            // Uncomment one of the following alternatives
            // $bytes /= pow(1024, $pow);
            // $bytes /= (1 << (10 * $pow)); 

            return round($bytes, $precision) . ' ' . $units[$pow]; 
        };
        
        $valueSet = [[0], [1000], [500050], [323241234], [5000000]];
        
        $this->assertConvertsAndRecompilesCorrectly($formatter, $valueSet);
    }
}