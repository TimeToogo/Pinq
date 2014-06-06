<?php

namespace Pinq\Tests\Integration\Collection;

class RemoveWhereTest extends CollectionTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred([$collection, 'removeWhere']);
        }
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatRemoveWhereRemovesItemsWhereTheFunctionReturnsTrueAndPreservesKeys(\Pinq\ICollection $numbers, array $data)
    {
        $predicate =
                function ($i, $k) {
                    return $i % 2 === 0;
                };

        $numbers->removeWhere($predicate);

        foreach ($data as $key => $value) {
            if ($predicate($value, $key)) {
                unset($data[$key]);
            }
        }

        $this->assertMatches($numbers, $data);
    }

    /**
     * @dataProvider everything
     */
    public function testThatRemoveWhereTrueRemovesAllItems(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeWhere(function () { return true; });

        $this->assertMatchesValues($collection, []);
    }

    /**
     * @dataProvider everything
     */
    public function testThatRemoveWhereFalseRemovesNoItems(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeWhere(function () { return false; });

        $this->assertMatchesValues($collection, $data);
    }
}
