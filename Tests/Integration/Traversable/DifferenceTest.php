<?php 

namespace Pinq\Tests\Integration\Traversable;

class DifferenceTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->difference([]);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatDifferenceWithSelfReturnsAnEmptyArray(\Pinq\ITraversable $traversable, array $data)
    {
        $intersection = $traversable->difference($traversable);
        
        $this->assertMatches($intersection, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatDifferenceWithEmptyReturnsSameAsTheOriginal(\Pinq\ITraversable $traversable, array $data)
    {
        $intersection = $traversable->difference(new \Pinq\Traversable());
        
        $this->assertMatches($intersection, array_unique($data));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatDifferenceWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $intersection = $traversable->difference($otherData);
        
        $this->assertMatches($intersection, array_diff($data, $otherData));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatDifferenceWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = [0 => 'test', 2 => 0.01, 5 => 4, 'test' => 1];
        $intersection = $traversable->difference($otherData);
        
        $this->assertMatches($intersection, array_diff($data, $otherData));
    }
}