<?php

namespace Pinq\Tests\Integration\Collection;

class SetIndexTest extends CollectionTest
{
    /**
     * @dataProvider everything
     */
    public function testThatSettingAnIndexWillOverrideTheElementInTheCollection(\Pinq\ICollection $collection, array $data)
    {
        reset($data);
        $key = key($data);

        if ($key === null) {
            return;
        }

        $instance = new \stdClass();
        $collection[$key] = $instance;
        $data[$key] = $instance;

        $this->assertMatches($collection, $data);
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatSetIndexWithNoKeyAppendsTheValueWithTheNextLargestIntGreaterThanOrEqualToZeroLikeAnArray(\Pinq\ICollection $collection, array $data)
    {
        $collection->clear();

        $collection[-5] = 'foo';
        $collection[] = 'bar';
        $collection[7] = 'baz';
        $collection[] = 'qux';

        $this->assertSame('foo', $collection[-5]);
        $this->assertSame('bar', $collection[0]);
        $this->assertSame('baz', $collection[7]);
        $this->assertSame('qux', $collection[8]);

        unset($collection[8]);

        $this->assertFalse(isset($collection[8]));

        $collection[] = 'qux1';

        $this->assertSame('qux1', $collection[8]);

        unset($collection[8], $collection[7]);
        $collection[] = 'boo';

        $this->assertSame('boo', $collection[1]);
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
        $collection[clone $anotherDateTime] = 3;

        $this->assertSame(1, $collection[$dateTime]);
        $this->assertSame(3, $collection[$anotherDateTime]);
        $this->assertSame(3, $collection[clone $anotherDateTime]);
    }
}
