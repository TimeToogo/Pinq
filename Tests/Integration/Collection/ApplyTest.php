<?php 

namespace Pinq\Tests\Integration\Collection;

class ApplyTest extends CollectionTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred([$collection, 'apply']);
        }
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatCollectionApplyOperatesOnTheSameCollection(\Pinq\ICollection $collection, array $data)
    {
        $multiply = 
                function (&$i) {
                    $i *= 10;
                };
                
        $collection->apply($multiply);
        array_walk($data, $multiply);
        
        $this->assertMatches($collection, $data);
    }
}