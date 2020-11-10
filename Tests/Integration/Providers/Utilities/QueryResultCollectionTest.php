<?php

namespace Pinq\Tests\Integration\Providers\Utilities;

use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\ITraversable;
use Pinq\PinqException;
use Pinq\Providers\Traversable;
use Pinq\Providers\Utilities\QueryResultCollection;
use Pinq\Tests\PinqTestCase;

class QueryResultCollectionTest extends PinqTestCase
{
    /**
     * @var QueryResultCollection
     */
    protected $queryResultsCollection;

    /**
     * @var IQueryable
     */
    protected $queryable;

    protected function setUp(): void
    {
        $this->queryResultsCollection = new QueryResultCollection();
        $this->queryable              = (new Traversable\Provider(\Pinq\Traversable::from([])))->createQueryable();
    }

    protected function assertComputesResult(O\Expression $expression, $result)
    {
        $this->assertTrue($this->queryResultsCollection->tryComputeResults($expression, $computedResults));
        $this->assertSame($result, $computedResults);
        $this->assertSame($result, $this->queryResultsCollection->computeResults($expression));
    }

    protected function assertCannotComputesResult(O\Expression $expression)
    {
        $this->assertFalse($this->queryResultsCollection->tryComputeResults($expression, $computedResults));
        $this->assertNull($computedResults);

        try {
            $this->queryResultsCollection->computeResults($expression);
            $this->assertTrue(false, 'Should have thrown an exception');
        } catch (PinqException $exception) {
        }
    }

    public function testCachesSavedResults()
    {
        $instance = new \stdClass();
        $sourceExpression = $this->queryable->getExpression();

        $this->queryResultsCollection->saveResults($sourceExpression, $instance);

        $this->assertComputesResult($sourceExpression, $instance);

        //Test overwrite
        $this->queryResultsCollection->saveResults($sourceExpression, [1, 2]);

        $this->assertComputesResult($sourceExpression, [1, 2]);
    }

    public function testClearRemovesAllSavedResults()
    {
        $this->queryResultsCollection->saveResults($sourceExpression = $this->queryable->getExpression(), 'test');
        $this->queryResultsCollection->saveResults($keysExpression = $this->queryable->keys()->getExpression(), 'keys');

        $this->assertComputesResult($sourceExpression, 'test');
        $this->assertComputesResult($keysExpression, 'keys');

        $this->queryResultsCollection->clearResults();

        $this->assertCannotComputesResult($sourceExpression);
        $this->assertCannotComputesResult($keysExpression);
    }

    public function testRemovesCorrectSavedResults()
    {
        $this->queryResultsCollection->saveResults(
                $takeExpression = $this->queryable->take(1)->getExpression(),
                ['take']
        );
        $this->queryResultsCollection->saveResults(
                $keysExpression = $this->queryable->keys()->getExpression(),
                ['keys']
        );

        $this->assertComputesResult($takeExpression, ['take']);
        $this->assertComputesResult($keysExpression, ['keys']);

        $this->queryResultsCollection->removeResults($keysExpression);

        $this->assertComputesResult($takeExpression, ['take']);
        $this->assertCannotComputesResult($keysExpression);

        $this->queryResultsCollection->removeResults($takeExpression);

        $this->assertCannotComputesResult($takeExpression);
    }

    public function testOptimizeQueryReturnsExpression()
    {
        $this->assertInstanceOf(
                O\Expression::getType(),
                $this->queryResultsCollection->optimizeQuery($this->queryable->getExpression())
        );
        $this->assertInstanceOf(
                O\Expression::getType(),
                $this->queryResultsCollection->optimizeQuery($this->queryable->take(1)->select('strlen')->getExpression())
        );
    }

    public function testCannotComputeWithUnapplicableSavedResults()
    {
        $sourceExpression = $this->queryable->getExpression();

        $this->assertFalse($this->queryResultsCollection->tryComputeResults($sourceExpression, $results));
        $this->assertNull($results);

        //Test with another expression
        $separateExpression = $this->queryable->skip(5)->getExpression();
        $this->queryResultsCollection->saveResults($separateExpression, 10);

        $this->assertCannotComputesResult($sourceExpression);
        $this->assertComputesResult($separateExpression, 10);
    }

    public function testComputesResultsFromSavedParentRequests()
    {
        $sourceExpression = $this->queryable->getExpression();

        $this->queryResultsCollection->saveResults($sourceExpression, range(1, 10));

        $subscopeExpression = $this->queryable->skip(5)->getExpression();

        $this->assertTrue($this->queryResultsCollection->tryComputeResults($subscopeExpression, $results));
        $this->assertInstanceOf(ITraversable::ITRAVERSABLE_TYPE, $results);
        /** @var $results ITraversable */
        $this->assertSame(
                [5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10],
                $results->asArray()
        );
    }

    public function testComputesResultsFromUsingMostApplicableParentRequest()
    {
        $sourceExpression = $this->queryable->getExpression();

        $this->queryResultsCollection->saveResults(
                $sourceExpression,
                \Pinq\Traversable::from([1])->select(
                        function () {
                            $this->assertTrue(false, 'Source expression results should not be called');
                        }
                )->getIterator()
        );

        $subscopeQueryable = $this->queryable->skip(5);
        $this->queryResultsCollection->saveResults($subscopeQueryable->getExpression(), [5, 6, 7, 8, 0]);

        $subSubscopeQueryable = $subscopeQueryable
                ->select(
                        function ($i) {
                            return $i * 10;
                        }
                )
                ->indexBy(
                        function ($i) {
                            return $i / 5;
                        }
                );

        $this->assertTrue(
                $this->queryResultsCollection->tryComputeResults($subSubscopeQueryable->getExpression(), $results)
        );
        $this->assertInstanceOf(ITraversable::ITRAVERSABLE_TYPE, $results);
        /** @var $results ITraversable */
        $this->assertSame([10 => 50, 12 => 60, 14 => 70, 16 => 80, 0 => 0], $results->asArray());
    }
}
