<?php

namespace Pinq\Tests\Integration\Traversable;

use Pinq\Iterators\IIteratorScheme;

class IteratorSchemeTest extends TraversableTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatReturnsAmIteratorScheme(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertInstanceOf(IIteratorScheme::IITERATOR_SCHEME_TYPE, $traversable->getIteratorScheme());
    }

    /**
     * @dataProvider theImplementations
     */
    public function testThatMaintainsCorrectSameIteratorScheme(\Pinq\ITraversable $traversable, array $data)
    {
        $originalScheme = $traversable->getIteratorScheme();

        $queriedTraversable = $traversable
                ->where(function () { throw new \Exception('This should never be executed'); })
                ->append([])
                ->difference([])
                ->except([])
                ->groupBy(function () { return 1; })
                ->groupJoin([])
                        ->on(function () { return true; })
                        ->to(function () { return 1; })
                ->indexBy(function () { return 1; })
                ->intersect([])
                ->join([])
                        ->on(function () { return true; })
                        ->to(function () { return 1; })
                ->keys()
                ->orderBy(function () { return 1; }, \Pinq\Direction::ASCENDING)
                ->thenByDescending(function () { return 1; })
                ->reindex()
                ->select(function () { return 1; })
                ->selectMany(function () { return []; })
                ->skip(1)
                ->take(5)
                ->union([])
                ->where(function () { return true; })
                ->whereIn([]);
                //Well that was fun

        $this->assertSame($originalScheme, $queriedTraversable->getIteratorScheme());
    }
}
