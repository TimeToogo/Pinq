<?php

namespace Pinq\Tests\Integration\Providers;

use \Pinq\Queries;
use \Pinq\Queries\Requests;

class LoadableRequestEvaluatorTest extends \Pinq\Tests\PinqTestCase
{
    public function RequestsToLoad()
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
            [new Requests\Sum(), 'LoadSum'],
        ];
    }
    
    /**
     * @dataProvider RequestsToLoad
     */
    public function testThatWillCallTheLoadMethodButNotWhenLoaded(Queries\IRequest $Request, $LoadMethod, $ReturnValue = null)
    {
        $RequestEvaluatorMock = $this->getMockForAbstractClass('\Pinq\Providers\Loadable\RequestEvaluator');
        
        $MethodMock = $RequestEvaluatorMock
                ->expects($this->once())
                ->method($LoadMethod)
                ->with($this->equalTo($Request));
        
        if($MethodMock !== null) {
            $MethodMock->will($this->returnValue($ReturnValue));
        }
        
        $LoadValuesRequest = new Requests\Values();
        $RequestEvaluatorMock
                ->expects($this->once())
                ->method('LoadValues')
                ->with($this->equalTo($LoadValuesRequest))
                ->will($this->returnValue($ReturnValue ?: [null]));
        
        $RequestEvaluatorMock->Visit($Request);
        
        //Load values
        $RequestEvaluatorMock->Visit($LoadValuesRequest);
        
        //Should not be called a second time
        $RequestEvaluatorMock->Visit($Request);
    }
}
