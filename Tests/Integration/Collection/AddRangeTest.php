<?php

namespace Pinq\Tests\Integration\Collection;

class AddRangeTest extends CollectionTest
{
    /**
     * @dataProvider assocMixedValues
     */
    public function testThatAddRangeAddAllValuesToCollection(\Pinq\ICollection $collection, array $data)
    {
        $newData = [1, 2, 3, 4 ,5];
        $collection->addRange($newData);

        $this->assertMatchesValues($collection, array_merge($data, $newData));
    }

    /**
     * @dataProvider assocMixedValues
     */
    public function testThatAddRangeReindexesCollection(\Pinq\ICollection $collection, array $data)
    {
        $newData = [1, 2, 3, 4 ,5];

        $collection->addRange($newData);
        $amountOfValues = count($data) + count($newData);

        $this->assertEquals(
                $amountOfValues === 0 ? [] : range(0, $amountOfValues - 1),
                array_keys($collection->asArray()));
    }

    /**
     * @dataProvider oneToTen
     * @expectedException \Pinq\PinqException
     */
    public function testThatInvalidValueThrowsExceptionWhenCallingAddRange(\Pinq\ICollection $collection, array $data)
    {
        $collection->addRange(1);
    }
}
