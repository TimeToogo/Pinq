<?php

namespace Pinq\Tests\Integration\Traversable;

class UnsupportedTest extends TraversableTest
{
    /**
     * @dataProvider everything
     */
    public function testThatSetIndexThrowsAndException(\Pinq\ITraversable $traversable, array $data)
    {
        if (!$traversable instanceof \Pinq\ICollection) {
            $this->expectException('\\Pinq\\PinqException');
            $traversable[0] = null;
        }
    }

    /**
     * @dataProvider everything
     */
    public function testThatUnsetIndexThrowsAndException(\Pinq\ITraversable $traversable, array $data)
    {
        if (!$traversable instanceof \Pinq\ICollection) {
            $this->expectException('\\Pinq\\PinqException');
            unset($traversable[0]);
        }
    }
}
