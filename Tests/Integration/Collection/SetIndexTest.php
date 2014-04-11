<?php

namespace Pinq\Tests\Integration\Collection;

class SetIndexTest extends CollectionTest
{
    
    /**
     * @dataProvider Everything
     */
    public function testThatSetingAnIndexWillOverrideTheElementInTheCollection(\Pinq\ICollection $Collection, array $Data)
    {
        reset($Data);
        $Key = key($Data);
        if($Key === null) {
            return;
        }
        
        $Instance = new \stdClass();
        
        $Collection[$Key] = $Instance;
        $Data[$Key] = $Instance;
        
        $this->AssertMatches($Collection, $Data);
    }
}
