<?php 

namespace Pinq\Tests\Integration\Providers;

use Pinq\Queries;
use Pinq\Queries\Requests;

class LoadableRequestEvaluatorTest extends \Pinq\Tests\PinqTestCase
{
    public function requestsToLoad()
    {
        return [
            [new Requests\Values(), 'LoadValues', []],
            [new Requests\Aggregate(new \Pinq\FunctionExpressionTree(null, [], [])), 'LoadAggregate'],
            [new Requests\All(), 'LoadAll'],
            [new Requests\Any(), 'LoadAny'],
            [new Requests\Average(), 'LoadAverage'],
            [new Requests\Contains(null), 'LoadContains'],
            [new Requests\Count(), 'LoadCount'],
            [new Requests\Exists(), 'LoadExists'],
            [new Requests\First(), 'LoadFirst'],
            [new Requests\GetIndex(0), 'LoadGetIndex'],
            [new Requests\IssetIndex(0), 'LoadIssetIndex'],
            [new Requests\Implode(''), 'LoadImplode'],
            [new Requests\Last(), 'LoadLast'],
            [new Requests\Maximum(), 'LoadMaximum'],
            [new Requests\Minimum(), 'LoadMinimum'],
            [new Requests\Sum(), 'LoadSum']
        ];
    }
    
    /**
     * @dataProvider RequestsToLoad
     */
    public function testThatWillCallTheLoadMethodButNotWhenLoaded(Queries\IRequest $request, $loadMethod, $returnValue = null)
    {
        $requestEvaluatorMock = $this->getMockForAbstractClass('\\Pinq\\Providers\\Loadable\\RequestEvaluator');
        
        $methodMock = $requestEvaluatorMock
                ->expects($this->once())
                ->method($loadMethod)
                ->with($this->equalTo($request));
        
        if ($methodMock !== null) {
            $methodMock->will($this->returnValue($returnValue));
        }
        
        $loadValuesRequest = new Requests\Values();
        
        $requestEvaluatorMock
                ->expects($this->once())
                ->method('LoadValues')
                ->with($this->equalTo($loadValuesRequest))
                ->will($this->returnValue($returnValue ?: [null]));
        
        $requestEvaluatorMock->visit($request);
        //Load values
        $requestEvaluatorMock->visit($loadValuesRequest);
        //Should not be called a second time
        $requestEvaluatorMock->visit($request);
    }
}