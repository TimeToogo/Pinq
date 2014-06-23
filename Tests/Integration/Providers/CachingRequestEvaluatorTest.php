<?php

namespace Pinq\Tests\Integration\Providers;

use Pinq\Queries;
use Pinq\Queries\Requests;

class CachingRequestEvaluatorTest extends \Pinq\Tests\PinqTestCase
{
    public function requestsToCache()
    {
        return [
            [new Requests\Values(), 'VisitValues'],
            [new Requests\Aggregate(new \Pinq\FunctionExpressionTree(null, [], [])), 'VisitAggregate'],
            [new Requests\All(), 'VisitAll'],
            [new Requests\Any(), 'VisitAny'],
            [new Requests\Average(), 'VisitAverage'],
            [new Requests\Contains(null), 'VisitContains'],
            [new Requests\Count(), 'VisitCount'],
            [new Requests\IsEmpty(), 'VisitIsEmpty'],
            [new Requests\First(), 'VisitFirst'],
            [new Requests\GetIndex(0), 'VisitGetIndex'],
            [new Requests\IssetIndex(0), 'VisitIssetIndex'],
            [new Requests\Implode(''), 'VisitImplode'],
            [new Requests\Last(), 'VisitLast'],
            [new Requests\Maximum(), 'VisitMaximum'],
            [new Requests\Minimum(), 'VisitMinimum'],
            [new Requests\Sum(), 'VisitSum']
        ];
    }

    /**
     * @dataProvider requestsToCache
     */
    public function testThatWillCallTheInnerEvaluatorOnceAndCacheTheResult(Queries\IRequest $request, $calledMethod)
    {
        $innerRequestEvaluatorMock = $this->getMock('\\Pinq\\Queries\\Requests\\RequestVisitor');
        $returnValue = new \stdClass();

        $innerRequestEvaluatorMock
                ->expects($this->once())
                ->method($calledMethod)
                ->with($request)
                ->will($this->returnValue($returnValue));

        $cachingRequestEvaluator = new \Pinq\Providers\Caching\RequestEvaluator($innerRequestEvaluatorMock);

        $firstReturn = $cachingRequestEvaluator->visit($request);
        //Inner evaluator should not be called a second time and result should be cached
        $secondReturn = $cachingRequestEvaluator->visit($request);

        $this->assertSame($returnValue, $firstReturn);
        $this->assertSame($returnValue, $secondReturn);
    }
}
