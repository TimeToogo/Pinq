<?php

namespace Pinq\Tests\Integration\Traversable;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\SchemeProvider;
use Pinq\Traversable;

class IteratorSchemeTest extends TraversableTest
{
    public function schemes()
    {
        $schemes = [];

        foreach (SchemeProvider::getAvailableSchemes() as $scheme) {
            $schemes[] = [$scheme];
        }

        return $schemes;
    }

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

    /**
     * @dataProvider schemes
     */
    public function testThatArrayCompatibleIsNotUsedForArrays(IIteratorScheme $scheme)
    {
        $iterator = Traversable::from([1,2,3], $scheme)
                ->where(function () { return true; })
                ->select(function ($v) { return $v; })
                ->getIterator();

        $this->assertNotInstanceOf(get_class($scheme->arrayCompatibleIterator(new \EmptyIterator())), $iterator);
    }

    /**
     * @dataProvider schemes
     */
    public function testThatArrayCompatibleIsUsedForIndexBy(IIteratorScheme $scheme)
    {
        $iterator = Traversable::from([1,2,3], $scheme)
                ->indexBy(function () { return new \stdClass(); })
                ->getIterator();

        $this->assertInstanceOf(get_class($scheme->arrayCompatibleIterator(new \EmptyIterator())), $iterator);
    }
}
