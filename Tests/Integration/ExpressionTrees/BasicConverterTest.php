<?php 

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Expressions as O;

class BasicConverterTest extends ConverterTest
{
    /**
     * @dataProvider Converters
     */
    public function testEmptyFunction()
    {
        $this->assertConvertsAndRecompilesCorrectly(function () {
            
        }, []);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testConstantReturnValueFunctions()
    {
        $this->assertConvertsAndRecompilesCorrectly(function () { return 1; }, [], O\Expression::value(1));
        
        $this->assertConvertsAndRecompilesCorrectly(function () { return 2; }, [], O\Expression::value(2));
        
        $this->assertConvertsAndRecompilesCorrectly(function () { return '1'; }, [], O\Expression::value('1'));
        
        $this->assertConvertsAndRecompilesCorrectly(function () { return 1.01; }, [], O\Expression::value(1.01))
                ;
        $this->assertConvertsAndRecompilesCorrectly(function () { return true; }, [], O\Expression::value(true));
        
        $this->assertConvertsAndRecompilesCorrectly(function () { return null; }, [], O\Expression::value(null));
        
        $this->assertConvertsAndRecompilesCorrectly(function () { return [1, 5, 57, 4 => 3, 'tset' => 'ftest']; }, [], O\Expression::arrayExpression(
                        [null, null, null, O\Expression::value(4), O\Expression::value('tset')],
                        [O\Expression::value(1), O\Expression::value(5), O\Expression::value(57), O\Expression::value(3), O\Expression::value('ftest')]));
        
        $this->assertConvertsAndRecompilesCorrectly(function () { return new \stdClass(); }, [], O\Expression::newExpression(O\Expression::value('stdClass')));
                
    }
    
    /**
     * @dataProvider Converters
     */
    public function testBinaryOperations()
    {
        $valueSet = [[-500], [-5], [-2], [-1], [1], [2],  [5], [500]];
        
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i + 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i - 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i * 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i / 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i % 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i & 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i | 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i << 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i >> 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i && 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i || 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i === 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i !== 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i == 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i != 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i > 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i >= 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i < 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i <= 1; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i . 1; }, $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testUnaryOperations()
    {
        $valueSet = [[1], [0]];
        
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return -$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return !$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return ~$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return +$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i++; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i--; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return ++$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return --$i; }, $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testCastOperations()
    {
        $valueSet = [[1], [0], [true], [false]];
        
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return (string)$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return (bool)$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return (int)$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return (array)$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return (object)$i; }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return (double)$i; }, $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testLanguageConstructs()
    {
        $valueSet = [[''], ['1'], ['test'], [null], [true], [false], [['dsffd']], [[]], [0]];
        
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return isset($i); }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return empty($i); }, $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testTernary()
    {
        $valueSet = [[1], [0], [-1], [-5], [5], [-500], [500]];
        
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return $i > 0 ? 5 : -50; }, $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testObjectMethodCall()
    {
        $valueSet = [[new \DateTime()], [new \DateTime('-3 days')], [new \DateTime('+2 weeks')]];
        
        $this->assertConvertsAndRecompilesCorrectly(
                function (\DateTime $i) {
                    return $i->format(\DateTime::ATOM);
                },
                $valueSet);
                
        $this->assertConvertsAndRecompilesCorrectly(
                function (\DateTime $i) {
                    return $i->getTimestamp();
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testObjectField()
    {
        $valueSet = [
            [(object)['prop' => null, 'foo' => null]], 
            [(object)['prop' => true, 'foo' => false]], 
            [(object)['prop' => 'fdsfdsdf', 'foo' => 'fewfew']]
        ];
        
        $this->assertConvertsAndRecompilesCorrectly(
                function (\stdClass $i) {
                    return $i->prop;
                },
                $valueSet);
                
        $this->assertConvertsAndRecompilesCorrectly(
                function (\stdClass $i) {
                    return $i->foo;
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testValueIndex()
    {
        $valueSet = [
            [['prop' => null, 'foo' => null]], 
            [['prop' => true, 'foo' => false]], 
            [['prop' => 'fdsfdsdf', 'foo' => 'fewfew']]
        ];
        
        $this->assertConvertsAndRecompilesCorrectly(
                function (array $i) {
                    return $i['prop'];
                },
                $valueSet);
                
        $this->assertConvertsAndRecompilesCorrectly(
                function (array $i) {
                    return $i['foo'];
                },
                $valueSet);
    }
    
    /**
     * @dataProvider Converters
     */
    public function testFunctionCall()
    {
        $valueSet = [
            [''],
            ['1'],
            ['test'],
            ['fooo'],
            ['geges ges  gse e'],
            ['striiiiiiiiiiing']
        ];
        
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return strlen($i); }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return str_split($i); }, $valueSet);
        $this->assertConvertsAndRecompilesCorrectly(function ($i) { return  explode(' ', $i); }, $valueSet);
    }
    
    /**
     * @dataProvider Converters
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
     * @dataProvider Converters
     */
    public function testStaticMethodCall()
    {
        $valueSet = [[range(0, 10)], [range(-100, 100)], [[1, '2', 3, '4', 5, '6', 7, '8', 9, '0']]];
        
        $this->assertConvertsAndRecompilesCorrectly(
                function (array $i) {
                    return \SplFixedArray::fromArray($i);
                },
                $valueSet);
    }
}