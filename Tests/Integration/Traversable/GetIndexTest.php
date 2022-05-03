<?php

namespace Pinq\Tests\Integration\Traversable;

class GetIndexTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatIndexReturnsCorrectValue(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($data as $key => $value) {
            $this->assertSame($value, $traversable[$key]);
        }
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatIndexesSupportObjectKeys(\Pinq\ITraversable $traversable, array $data)
    {
        //Make object references
        $instances = [];
        foreach ($data as $key => $i) {
            $instances[$key] = (object) ['bar' => $key];
        }

        $traversable = $traversable->indexBy(function ($value, $key) use (&$instances) {
            return $instances[$key];
        });

        //Load instance keys
        $traversable->asArray();

        foreach ($data as $key => $value) {
            //Should be using object identity (reference type)
            $this->assertFalse(isset($traversable[(object) ['bar' => $key]]));

            $this->assertTrue(isset($traversable[$instances[$key]]));
            $this->assertSame($value, $traversable[$instances[$key]]);
        }
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatIndexesSupportArrayKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable->indexBy(function ($value, $key) {
            return ['foo' => $key, 2 => 3];
        });

        foreach ($data as $key => $value) {
            //Arrays are value types, no reference required
            $this->assertTrue(isset($traversable[['foo' => $key, 2 => 3]]));
            $this->assertSame($value, $traversable[['foo' => $key, 2 => 3]]);

            $this->assertFalse(isset($traversable[['foo' => $key, 2 => '3']]),
                'Should be using strict equality for arrays (order matters)');
            $this->assertFalse(isset($traversable[[2 => 3, 'foo' => $key]]),
                    'Should be using strict equality for arrays (order matters)');
        }
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatDateTimeIndexesAreComparedByValueAndClass(\Pinq\ITraversable $traversable, array $data)
    {
        $dateTime        = new \DateTime('2-1-2001');
        $anotherDateTime = new \DateTime('2-1-2000');

        $traversable = $traversable
                ->append([$dateTime, $anotherDateTime])
                ->indexBy(function ($value) { return $value; })
                ->select(function (\DateTime $dateTime) { return $dateTime->format('j-n-Y'); });

        $this->assertSame('2-1-2001', $traversable[$dateTime]);
        $this->assertSame('2-1-2001', $traversable[clone $dateTime]);
        $this->assertSame('2-1-2001', $traversable[new \DateTime('2-1-2001')]);

        if(class_exists('DateTimeImmutable', false)) {
            $this->assertNull($traversable[new \DateTimeImmutable('2-1-2001')]);
        }
    }
}
