<?php

namespace Pinq\Tests\Integration\Collection;

class ExtendedCollection extends \Pinq\Collection
{
    const EXTENDED_TYPE = __CLASS__;

    public function four()
    {
        return 4;
    }
}

class ExtendedCollectionTest extends CollectionTest
{
    public function extendedCollection()
    {
        return [[new ExtendedCollection()]];
    }

    private function assertExtended(\Pinq\ICollection $collection)
    {
        $this->assertSame(ExtendedCollection::EXTENDED_TYPE, get_class($collection));
        $this->assertSame(4, $collection->four());
    }

    /**
     * @dataProvider extendedCollection
     */
    public function testThatExtendedClassIsMaintainedAfterWhereQuery(\Pinq\ICollection $collection)
    {
        $collection = $collection->where(function () { return true; });

        $this->assertExtended($collection);
    }

    /**
     * @dataProvider extendedCollection
     */
    public function testThatExtendedClassIsMaintainedAfterOrderByQuery(\Pinq\ICollection $collection)
    {
        $collection = $collection
                ->orderByAscending(function () { })
                ->thenByDescending(function () { });

        $this->assertExtended($collection);
    }

    /**
     * @dataProvider extendedCollection
     */
    public function testThatExtendedClassIsMaintainedAfterGroupByQuery(\Pinq\ICollection $collection)
    {
        $collection = $collection
                ->groupBy(function () { });

        $this->assertExtended($collection);
    }

    /**
     * @dataProvider extendedCollection
     */
    public function testThatExtendedClassIsMaintainedAfterJoinQuery(\Pinq\ICollection $collection)
    {
        $collection = $collection
                ->join([])
                ->on(function () { return true; })
                ->to(function () { });

        $this->assertExtended($collection);
    }

    /**
     * @dataProvider extendedCollection
     */
    public function testThatExtendedClassIsMaintainedAfterLongQuery(\Pinq\ICollection $collection)
    {
        $collection = $collection
                ->append([1,2,4,5])
                ->join([6,7,8,9])
                ->on(function () { return true; })
                ->to(function ($a, $b) { return $a + $b; })
                ->unique()
                ->select(function ($i) { return $i * 10; })
                ->indexBy(function ($i) { return $i; });

        $this->assertExtended($collection);
    }
}
