<?php

namespace Pinq\Tests\Integration\Collection;

use Pinq\PinqException;

class RemoveRangeTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatRemoveRangeRemovesAllValuesFromCollection(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeRange($collection->asArray());
        $this->assertMatchesValues($collection, []);
    }

    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatRemoveRangeWillRemovesIdenticalValuesFromCollectionAndPreserveKeys(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeRange([1, '2']);

        foreach ($data as $key => $value) {
            if ($value === 1 || $value === '2') {
                unset($data[$key]);
            }
        }

        $this->assertMatchesValues($collection, $data);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatInvalidValueThrowsExceptionWhenCallingRemoveRange(\Pinq\ICollection $collection, array $data)
    {
        $this->expectException(PinqException::class);
        $collection->removeRange(1);
    }
}
