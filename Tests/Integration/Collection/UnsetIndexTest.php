<?php

namespace Pinq\Tests\Integration\Collection;

class UnsetIndexTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatUnsetingAnIndexWillRemoveTheElementFromTheCollection(\Pinq\ICollection $collection, array $data)
    {
        reset($data);
        $key = key($data);

        if ($key === null) {
            return;
        }

        unset($collection[$key], $data[$key]);

        $this->assertMatches($collection, $data);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatDateTimeIndexesAreComparedByValue(\Pinq\ICollection $collection, array $data)
    {
        $dateTime        = new \DateTime('2-1-2001');
        $anotherDateTime = new \DateTime('2-1-2000');

        $collection[$dateTime] = 1;
        $collection[$anotherDateTime] = 2;
        unset($collection[new \DateTime('2-1-2000')]);

        $this->assertTrue(isset($collection[$dateTime]));
        $this->assertFalse(isset($collection[$anotherDateTime]));
        $this->assertFalse(isset($collection[clone $anotherDateTime]));
        $this->assertFalse(isset($collection[new \DateTime('2-1-2000')]));
    }
}
