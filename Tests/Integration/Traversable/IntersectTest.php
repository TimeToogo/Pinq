<?php

namespace Pinq\Tests\Integration\Traversable;

class IntersectTest extends TraversableTest
{
    protected function TestReturnsNewInstance(\Pinq\ITraversable $Traversable)
    {
        return $Traversable->Intersect([]);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatIntersectWithSelfReturnsUniqueValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Intersection = $Traversable->Intersect($Traversable);
        
        $this->AssertMatches($Intersection, array_unique($Data));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatIntersectWithEmptyReturnsEmpty(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Intersection = $Traversable->Intersect(new \Pinq\Traversable());
        
        $this->AssertMatches($Intersection, []);
    }
    
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatIntersectionWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OtherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $Intersection = $Traversable->Intersect($OtherData);
        
        $this->AssertMatches($Intersection, array_intersect($Data, $OtherData));
    }
    
    /**
     * @dataProvider OneToTen
     */
    public function testThatIntersectUsesStrictEquality(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Insection = $Traversable->Intersect(['1', '2', '3', '4', '5']);
        
        $this->AssertMatches($Insection, []);
    }
}
