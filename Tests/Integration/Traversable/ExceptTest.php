<?php

namespace Pinq\Tests\Integration\Traversable;

class ExceptTest extends TraversableTest
{
    protected function _testReturnsNewInstance(\Pinq\ITraversable $traversable)
    {
        return $traversable->except([]);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithSelfReturnsAnEmptyArray(\Pinq\ITraversable $traversable, array $data)
    {
        $except = $traversable->except($traversable);

        $this->assertMatches($except, []);
    }

    /**
     * @dataProvider Everything
     */
    public function testThatExceptWithEmptyReturnsSameAsTheOriginal(\Pinq\ITraversable $traversable, array $data)
    {
        $except = $traversable->except(new \Pinq\Traversable());

        $this->assertMatches($except, $data);
    }

    /**
     * @dataProvider OneToTen
     */
    public function testThatExceptWithDuplicateValuesPreservesTheOriginalKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = ['test' => 1, 'anotherkey' => 3, 1000 => 5];
        $except = $traversable->except($otherData);

        $this->assertMatches($except, array_diff($data, $otherData));
    }

    /**
     * @dataProvider OneToTen
     */
    public function testThatExceptWithDuplicateKeysPreservesTheOriginalValues(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = [0 => 'test', 2 => 0.01, 5 => 4, 'test' => 1];
        $except = $traversable->except($otherData);

        $this->assertMatches($except, array_diff($data, $otherData));
    }

    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatExceptUsesStrictEquality(\Pinq\ITraversable $traversable, array $data)
    {
        $castToStringValues = array_map('strval', $data);
        $except = $traversable->except($castToStringValues);

        $this->assertMatches($except, $data);
    }
}
