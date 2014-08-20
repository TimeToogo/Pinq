<?php

namespace Pinq\Tests\Integration\Collection;

class ClearTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatClearRemovesAllItems(\Pinq\ICollection $collection, array $data)
    {
        $collection->clear();

        $this->assertCount(0, $collection);
        $this->assertMatchesValues($collection, []);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatClearRemovesAllScopedItems(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->where(function ($i) { return $i <= 5; })
                ->clear();

        $this->assertMatches($collection, [5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10]);
    }
}
