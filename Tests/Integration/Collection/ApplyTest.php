<?php

namespace Pinq\Tests\Integration\Collection;

class ApplyTest extends CollectionTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred([$collection, 'apply']);
        }
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatCollectionApplyOperatesOnTheSameCollection(\Pinq\ICollection $collection, array $data)
    {
        $multiply =
                function (&$i) {
                    $i *= 10;
                };

        $collection->apply($multiply);
        array_walk($data, $multiply);

        $this->assertMatches($collection, $data);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatCollectionApplyWorksOnScopedValues(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->where(function ($i) { return $i % 2 === 0; })
                ->apply(function (&$i) { $i *= 10; });
        
        $this->assertMatches($collection, [
            1,
            20,
            3,
            40,
            5,
            60,
            7,
            80,
            9,
            100
        ]);
    }
}
