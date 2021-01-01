<?php

namespace Pinq\Tests\Integration\Traversable;

use Pinq\Tests\Helpers\ExtendedDateTime;

class IssetIndexTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatIssetOnValidIndexesReturnTrue(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($data as $key => $value) {
            $this->assertTrue(isset($traversable[$key]));
        }
    }

    /**
     * @dataProvider everything
     */
    public function testThatIssetOnInvalidIndexesReturnFalse(\Pinq\ITraversable $traversable, array $data)
    {
        $notAnIndex = 0;

        while (isset($data[$notAnIndex])) {
            $notAnIndex++;
        }

        $this->assertFalse(isset($traversable[$notAnIndex]));
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
                ->indexBy(function ($value) { return $value; });

        $this->assertTrue(isset($traversable[$dateTime]));
        $this->assertTrue(isset($traversable[clone $dateTime]));
        $this->assertTrue(isset($traversable[new \DateTime('2-1-2001')]));
        $this->assertFalse(isset($traversable[new ExtendedDateTime('2-1-2001')]));
    }
}
