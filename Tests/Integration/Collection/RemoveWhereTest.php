<?php 

namespace Pinq\Tests\Integration\Collection;

class RemoveWhereTest extends CollectionTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred([$collection, 'RemoveWhere']);
        }
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatRemoveWhereRemovesItemsWhereTheFunctionReturnsTrueAndPreservesKeys(\Pinq\ICollection $numbers, array $data)
    {
        $predicate = 
                function ($i) {
                    return $i % 2 === 0;
                };
                
        $numbers->removeWhere($predicate);
        
        foreach ($data as $key => $value) {
            if ($predicate($value)) {
                unset($data[$key]);
            }
        }
        
        $this->assertMatches($numbers, $data);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatRemoveWhereTrueRemovesAllItems(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeWhere(function () { return true; });
        
        $this->assertMatchesValues($collection, []);
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatRemoveWhereFalseRemovesNoItems(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeWhere(function () { return false; });
        
        $this->assertMatchesValues($collection, $data);
    }
}