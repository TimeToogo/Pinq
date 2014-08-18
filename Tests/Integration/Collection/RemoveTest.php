<?php

namespace Pinq\Tests\Integration\Collection;

class RemoveTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatRemoveRemovesAllValuesFromCollection(\Pinq\ICollection $collection, array $data)
    {
        foreach ($collection->asArray() as $value) {
            $collection->remove($value);
        }
        $this->assertMatchesValues($collection, []);
    }

    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatRemoveWillRemovesIdenticalValuesFromCollectionAndPreserveKeys(
            \Pinq\ICollection $collection,
            array $data
    ) {
        $collection->remove(1);
        $collection->remove('2');

        foreach ($data as $key => $value) {
            if ($value === 1 || $value === '2') {
                unset($data[$key]);
            }
        }

        $this->assertMatchesValues($collection, $data);
    }
}
