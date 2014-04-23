<?php 

namespace Pinq\Tests\Integration\Collection;

class SetIndexTest extends CollectionTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatSetingAnIndexWillOverrideTheElementInTheCollection(\Pinq\ICollection $collection, array $data)
    {
        reset($data);
        $key = key($data);
        
        if ($key === null) {
            return;
        }
        
        $instance = new \stdClass();
        $collection[$key] = $instance;
        $data[$key] = $instance;
        $this->assertMatches($collection, $data);
    }
}