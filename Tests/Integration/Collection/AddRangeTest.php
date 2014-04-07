<?php

namespace Pinq\Tests\Integration\Collection;

class AddRangeTest extends CollectionTest
{
    
    /**
     * @dataProvider Everything
     */
    public function testThatAddRangeAddAllValuesToCollection(\Pinq\ICollection $Collection, array $Data)
    {
        $NewData = [1,2,3,4,5];
        $Collection->AddRange($NewData);
        
        $this->AssertMatchesValues($Collection, array_merge($Data, $NewData));
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAddRangeReindexesCollection(\Pinq\ICollection $Collection, array $Data)
    {
        $NewData = [1,2,3,4,5];
        $Collection->AddRange($NewData);
        
        $AmountOfValues = count($Data) + count($NewData);
        $this->assertEquals($AmountOfValues === 0 ? [] : range(0, $AmountOfValues - 1), array_keys($Collection->AsArray()));
    }
    
    /**
     * @dataProvider OneToTen
     * @expectedException \Pinq\PinqException
     */
    public function testThatInvalidValueThrowsExceptionWhenCallingAddRange(\Pinq\ICollection $Collection, array $Data)
    {
        $Collection->AddRange(1);
    }
}
