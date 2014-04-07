<?php

namespace Pinq\Tests\Integration\Collection;

class ClearTest extends CollectionTest
{
    
    /**
     * @dataProvider Everything
     */
    public function testThatClearRemovesAllItems(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->Clear();
        
        $this->AssertMatchesValues($Collection, []);
    }
}
