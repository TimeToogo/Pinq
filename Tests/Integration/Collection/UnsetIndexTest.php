<?php

namespace Pinq\Tests\Integration\Collection;

class UnsetIndexTest extends CollectionTest
{
    
    /**
     * @dataProvider Everything
     */
    public function testThatUnsetingAnIndexWillRemoveTheElementFromTheCollection(\Pinq\ICollection $Collection, array $Data)
    {
        reset($Data);
        $Key = key($Data);
        if($Key === null) {
            return;
        }
        unset($Collection[$Key], $Data[$Key]);
        
        $this->AssertMatches($Collection, $Data);
    }
}
