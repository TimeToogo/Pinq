<?php

namespace Pinq\Tests\Integration\Providers;

use \Pinq\Queries;
use \Pinq\Queries\Requests;

class CachingRequestEvaluatorTest extends \Pinq\Tests\PinqTestCase
{
    public function RequestsToCache()
    {
        return [
            [new Requests\Values(), 'VisitValues'],
            [new Requests\Aggregate(new \Pinq\FunctionExpressionTree(null, [], [])), 'VisitAggregate'],
            [new Requests\All(), 'VisitAll'],
            [new Requests\Any(), 'VisitAny'],
            [new Requests\Average(), 'VisitAverage'],
            [new Requests\Contains(null), 'VisitContains'],
            [new Requests\Count(), 'VisitCount'],
            [new Requests\Exists(), 'VisitExists'],
            [new Requests\First(), 'VisitFirst'],
            [new Requests\GetIndex(0), 'VisitGetIndex'],
            [new Requests\IssetIndex(0), 'VisitIssetIndex'],
            [new Requests\Implode(''), 'VisitImplode'],
            [new Requests\Last(), 'VisitLast'],
            [new Requests\Maximum(), 'VisitMaximum'],
            [new Requests\Minimum(), 'VisitMinimum'],
            [new Requests\Sum(), 'VisitSum'],
        ];
    }
    
    /**
     * @dataProvider RequestsToCache
     */
    public function testThatWillCallTheInnerEvaluatorOnceAndCacheTheResult(Queries\IRequest $Request, $CalledMethod)
    {
        $InnerRequestEvaluatorMock = $this->getMock('\Pinq\Queries\Requests\RequestVisitor');
        
        $ReturnValue = new \stdClass();
        $InnerRequestEvaluatorMock
                ->expects($this->once())
                ->method($CalledMethod)
                ->with($Request)
                ->will($this->returnValue($ReturnValue));
        
        $CachingRequestEvaluator =  new \Pinq\Providers\Caching\RequestEvaluator($InnerRequestEvaluatorMock);
                
        $FirstReturn = $CachingRequestEvaluator->Visit($Request);
        
        //Inner evaluator should not be called a second time and result should be cached
        $SecondReturn = $CachingRequestEvaluator->Visit($Request);
        
        $this->assertSame($ReturnValue, $FirstReturn);
        $this->assertSame($ReturnValue, $SecondReturn);
    }
}
