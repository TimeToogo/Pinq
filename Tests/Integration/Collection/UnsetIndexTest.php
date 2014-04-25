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
}
