<?php

namespace Pinq\Tests\Integration\Traversable;

use Pinq\Tests\Helpers\ExtendedDateTime;

class UnionTest extends TraversableTest
{
    protected function _testReturnsNewInstanceOfSameTypeWithSameScheme(\Pinq\ITraversable $traversable)
    {
        return $traversable->union([]);
    }

    /**
     * @dataProvider assocStrings
     */
    public function testThatUnionWithSelfReturnsUniqueReindexedValues(\Pinq\ITraversable $traversable, array $data)
    {
        $unioned = $traversable->union($traversable);

        $this->assertMatches($unioned, array_values(array_unique($data)));
    }

    /**
     * @dataProvider assocStrings
     */
    public function testThatUnionWithEmptyReturnsUniqueReindexedValues(\Pinq\ITraversable $traversable, array $data)
    {
        $unioned = $traversable->union([]);

        $this->assertMatches($unioned, array_values(array_unique($data)));
    }

    /**
     * @dataProvider oneToTenTwice
     */
    public function testThatUnionRemovesDuplicateValues(\Pinq\ITraversable $traversable, array $data)
    {
        $unioned = $traversable->union([]);

        $this->assertMatches($unioned, array_values(array_unique($data)));
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatUnionUsesStrictEquality(\Pinq\ITraversable $traversable, array $data)
    {
        $otherData = [100 => '1', 101 => '2', 102 => '3'];
        $unioned = $traversable->union($otherData);

        $this->assertMatches($unioned, array_merge($data, $otherData));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatUnionMaintainsReferences(\Pinq\ITraversable $traversable)
    {
        $data = $this->makeRefs(range('a', 'f'));

        $traversable
                ->union($data)
                ->iterate(function (&$i) { $i = "$i-"; });

        $this->assertSame('a-b-c-d-e-f-', implode('', $data));
    }

    /**
     * @dataProvider emptyData
     */
    public function testThatUnionWillMatchDateTimeByTimestampAndClass(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->append([
                        new \DateTime('2-1-2001'),
                        new \DateTime('2-1-2001'),
                        new \DateTime('2-1-2001 00:00:01'),
                        new \DateTime('1-1-2001'),
                        new \DateTime('1-1-2001'),
                        new \DateTime('3-1-2001'),
                        new ExtendedDateTime('1-1-2001'),
                ])
                ->union([
                        new \DateTime('4-1-2001'),
                        new \DateTime('2-1-2001'),
                        new ExtendedDateTime('1-1-2001'),
                        new ExtendedDateTime('2-1-2001'),
                ])
                ->select(function (\DateTime $outer) {
                    return get_class($outer) . ':' . $outer->format('d-m-Y H:i:s');
                });

        $this->assertMatchesValues($traversable, [
                'DateTime:02-01-2001 00:00:00',
                'DateTime:02-01-2001 00:00:01',
                'DateTime:01-01-2001 00:00:00',
                'DateTime:03-01-2001 00:00:00',
                'Pinq\Tests\Helpers\ExtendedDateTime:01-01-2001 00:00:00',
                'DateTime:04-01-2001 00:00:00',
                'Pinq\Tests\Helpers\ExtendedDateTime:02-01-2001 00:00:00',
        ]);
    }
}
