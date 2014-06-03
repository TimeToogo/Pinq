<?php

namespace Pinq\Tests\Integration\Traversable;

class NonScalarKeysIterator extends \IteratorIterator
{
    public function key()
    {
        return new \stdClass();
    }
}

class IterationTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatIndexReturnsCorrectValue(\Pinq\ITraversable $traversable, array $data)
    {
        $iteratedData = [];
        
        foreach ($traversable as $key => $value) {
            $iteratedData[$key] = $value;
        }
        
        $this->assertSame($data, $iteratedData);
    }
    
    /**
     * @dataProvider everything
     */
    public function testIteratePassedAllValuesAndKeysToFunction(\Pinq\ITraversable $traversable, array $data)
    {
        $iteratedData = [];
        
        $traversable->iterate(function ($value, $key) use (&$iteratedData) { 
                    $iteratedData[$key] = $value;
                });
        
        $this->assertSame($data, $iteratedData);
    }
    
    /**
     * @dataProvider everything
     */
    public function testThatNonScalarKeysAreReindexed(\Pinq\ITraversable $traversable, array $data)
    {
        foreach([new \stdClass(), [], [1], fopen('php://input', 'r')] as $nonScalar) {
            $withNonScalarKeys = $traversable
                    ->take(1)
                    ->indexBy(function () use ($nonScalar) { return $nonScalar; });

            $this->assertSame(empty($data) ? [] : [0 => reset($data)], $withNonScalarKeys->asArray());
        }
    }
    
    /**
     * @dataProvider everything
     */
    public function testThatIdenticalNonScalarKeysMapToTheSameScalarKey(\Pinq\ITraversable $traversable, array $data)
    {
        foreach([new \stdClass(), [], [1], fopen('php://input', 'r')] as $identicalNonScalar) {
            $withNonScalarKeys = $traversable
                    ->indexBy(function () use ($identicalNonScalar) { return $identicalNonScalar; });
                    
            $this->assertSame(empty($data) ? [] : [0 => end($data)], $withNonScalarKeys->asArray());
            
            if(is_object($identicalNonScalar) && !($traversable instanceof \Pinq\IQueryable)) {
                //No longer identical, should map to individual keys
                $withNonScalarKeys = $traversable
                        ->indexBy(function () use ($identicalNonScalar) { return clone $identicalNonScalar; });
                
                $this->assertSame(array_values($data), $withNonScalarKeys->asArray());
            }
        }
    }
    
    /**
     * @dataProvider everything
     */
    public function testThatNonScalarKeysAreReindexedWhenConvertingToArrayOrIteratingButNotForIterateMethodOrTrueIterator(\Pinq\ITraversable $traversable, array $data)
    {
        $nonScalarKeys = [
            4 => new \stdClass(),
            5 => [],
            7 => fopen('php://input', 'r')
        ];
        
        $withNonScalarKeys = $traversable
                ->take(0)
                ->append(range(1, 9))
                ->indexBy(function ($value) use ($nonScalarKeys) {
                    return isset($nonScalarKeys[$value]) ? 
                            $nonScalarKeys[$value] : 
                            //Should handle string intergers being auto cast to ints
                            (string)($value * 2);
                });
                
        $expectedData = [
            2 => 1, 
            4 => 2, 
            6 => 3,
            
            //1 + largest integer key
            7 => 4,
            
            //1 + largest integer key
            8 => 5,
            
            12 => 6,
            
            //1 + largest integer key
            13 => 7,
            
            16 => 8,
            18 => 9,
        ];
        
        $this->assertSame($expectedData, $withNonScalarKeys->asArray());
        
        $iteratedData = [];
        
        foreach($withNonScalarKeys as $key => $value) {
            $iteratedData[$key] = $value;
        }
        
        $this->assertSame($expectedData, $iteratedData);
        
        $assertCorrectKeyValuePair = function ($value, $key) use ($nonScalarKeys) {
            if(isset($nonScalarKeys[$value])) {
                $this->assertSame($nonScalarKeys[$value], $key);
            } else {
                $this->assertSame((string)($value * 2), $key);
            }
        };
        
        $withNonScalarKeys->iterate($assertCorrectKeyValuePair);
        
        $trueIterator = $withNonScalarKeys->getTrueIterator();
        $trueIterator->rewind();
        while ($trueIterator->valid()) {
            $assertCorrectKeyValuePair($trueIterator->current(), $trueIterator->key());
            $trueIterator->next();
        }
    }
}
