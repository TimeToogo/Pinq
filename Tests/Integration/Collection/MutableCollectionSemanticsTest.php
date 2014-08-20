<?php

namespace Pinq\Tests\Integration\Collection;

class MutableCollectionSemanticsTest extends CollectionTest
{
    /**
     * @dataProvider oneToTen
     */
    public function testThatCollectionRemovesRangeFromScopedValues(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->where(function ($i) { return $i >= 5; });

        $filteredScopeCollection->removeRange(range(3, 7));

        $this->assertMatchesValues($filteredScopeCollection, [8, 9, 10],
                'Scoped collection should not contain the removed applicable values');
        $this->assertMatchesValues($collection, [1, 2, 3, 4, 8, 9, 10],
                'Source collection should have removed applicable values from the scoped collection');
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatCollectionClearsOnlyScopedValues(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->where(function ($i) { return $i % 2 === 0; });
        $slicedScopeCollection = $filteredScopeCollection->slice(1, 3);

        $slicedScopeCollection->clear();

        $this->assertMatchesValues($slicedScopeCollection, [10]);
        $this->assertMatchesValues($filteredScopeCollection, [2, 10]);
        $this->assertMatchesValues($collection, [1, 2, 3, 5, 7, 9, 10]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatAddRangeAppendsToTheEndOfTheSourceCollections(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->where(function ($i) { return $i % 2 === 0; });
        $slicedScopeCollection = $filteredScopeCollection->slice(2, 4);

        $slicedScopeCollection->addRange([51, 70, 100]);

        $this->assertMatchesValues($slicedScopeCollection, [6, 8, 10, 70]);
        $this->assertMatchesValues($filteredScopeCollection, [2, 4, 6, 8, 10, 70, 100]);
        $this->assertMatchesValues($collection, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 51, 70, 100]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatRemoveWhereRemovesScopedValues(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->where(function ($i) { return $i % 3 === 0; });
        $slicedScopeCollection = $filteredScopeCollection->take(1);

        $slicedScopeCollection->removeWhere(function ($i) { return $i === 3; });

        $this->assertMatchesValues($slicedScopeCollection, [6]);
        $this->assertMatchesValues($filteredScopeCollection, [6, 9]);
        $this->assertMatchesValues($collection, [1, 2, 4, 5, 6, 7, 8, 9, 10]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatApplyUpdatesScopedValuesWithScopeRefreshing(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->whereIn([1, 4, 7, 9, 10]);//1, 4, 7, 9, 10
        $moreFilteredScopeCollection = $filteredScopeCollection->difference([4, 9]);//1, 7, 10

        //After apply: 1 -> 10, 7 -> 70, 10 -> 100, only 10 is still in the current scope.
        $moreFilteredScopeCollection->apply(function (&$i) { $i *= 10; });

        $this->assertMatchesValues($moreFilteredScopeCollection, [10]);
        $this->assertMatchesValues($filteredScopeCollection, [10, 4, 9]);
        $this->assertMatchesValues($collection, [10, 2, 3, 4, 5, 6, 70, 8, 9, 100]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatSetIndexSetsToSourceCollections(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->where(function ($i) { return $i % 2 === 0; });
        $slicedScopeCollection = $filteredScopeCollection->slice(1, 2);

        $slicedScopeCollection[5] = 100;

        $this->assertMatches($slicedScopeCollection, [3 => 4, 5 => 100]);
        $this->assertMatches($collection, [1, 2, 3, 4, 5, 100, 7, 8, 9, 10]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatUnsetIndexUnsetsFromAllCollections(\Pinq\ICollection $collection, array $data)
    {
        $filteredScopeCollection = $collection->where(function ($i) { return $i % 2 === 0; });
        $slicedScopeCollection = $filteredScopeCollection->slice(1, 2);

        unset($slicedScopeCollection[3]);

        $this->assertMatches($slicedScopeCollection, [5 => 6, 7 => 8]);
        $this->assertMatches($collection, [1, 2, 3, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10]);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatQueryUpdatesWhenSourceCollectionIsMutated(\Pinq\ICollection $collection, array $data)
    {
        $query = $collection
                ->where(function ($i) { return $i % 2 === 0; })
                ->orderByDescending(function ($i) { return $i; })
                ->groupJoin(range(1, 10, 2))
                    ->on(function ($i, $v) { return $v < $i; })
                    ->to(function ($i, \Pinq\ITraversable $nums) {
                        return $i . ':' . $nums->implode(',');
                    });

        $this->assertMatchesValues($query, [
            '10:1,3,5,7,9',
            '8:1,3,5,7',
            '6:1,3,5',
            '4:1,3',
            '2:1',
        ]);

        $collection->removeRange([4, 5]);
        unset($collection[7]);

        $this->assertMatchesValues($query, [
            '10:1,3,5,7,9',
            '6:1,3,5',
            '2:1',
        ]);
    }
}
