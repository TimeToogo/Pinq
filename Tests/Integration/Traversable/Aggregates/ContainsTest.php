<?php

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class ContainsTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider assocOneToTen
     */
    public function testThatContainsReturnsTrueForAContainedElement(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($data as $value) {
            $this->assertTrue($traversable->contains($value));
        }
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatContainsReturnsWhetherItContainsAnIdenticalElement(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertTrue($traversable->contains(1));
        $this->assertFalse($traversable->contains('1'));
        $this->assertTrue($traversable->contains(10));
        $this->assertFalse($traversable->contains('10'));
        $this->assertFalse($traversable->contains(-1));
        $this->assertFalse($traversable->contains(0));
        $this->assertFalse($traversable->contains(11));
    }
}
