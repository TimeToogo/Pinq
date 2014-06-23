<?php

namespace Pinq\Tests\Integration\Providers;

use Pinq\Queries;
use Pinq\Queries\Requests;

class LoadableProviderTest extends \Pinq\Tests\PinqTestCase
{
    public function testThatWillCacheParentScopeAndEvaluateSubscopeInMemory()
    {
        $values = new \ArrayIterator(range(1, 10));
        
        $requestEvaluatorMock = $this->getMockForAbstractClass('\\Pinq\\Providers\\Loadable\\RequestEvaluator');
        
        $requestEvaluatorMock
                ->expects($this->once())
                ->method('loadValues')
                ->will($this->returnValue($values));
        
        $queryProviderMock = $this->getMockForAbstractClass('\\Pinq\\Providers\\Loadable\\Provider');

        $queryProviderMock
                ->expects($this->once())
                ->method('loadRequestEvaluator')
                ->will($this->returnValue($requestEvaluatorMock));
        
        //Load values request to force load
        $unscopedValues = $queryProviderMock->load(new Queries\RequestQuery(new Queries\Scope([]), new Requests\Values()));
        
        $this->assertSame($values, $unscopedValues instanceof \Pinq\Iterators\IAdapterIterator ? $unscopedValues->getSourceIterator() : $unscopedValues);
        
        //Evaluate a sub request, should not call loadRequestEvaluator again but evaluate with a traversable provider
        $subscopeValues = $queryProviderMock->load(new Queries\RequestQuery(new Queries\Scope([new Queries\Segments\Range(5, null)]), new Requests\Values()));
        
        $this->assertSame(array_slice($values->getArrayCopy(), 5, null, true), iterator_to_array($subscopeValues));
    }
}
