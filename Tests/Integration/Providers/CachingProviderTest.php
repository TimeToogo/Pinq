<?php

namespace Pinq\Tests\Integration\Providers;

use Pinq\Queries;
use Pinq\Queries\Requests;

class CachingProviderTest extends \Pinq\Tests\PinqTestCase
{
    public function testThatWillReturnInnerProviderImplementation()
    {
        $functionExpressionTreeConverterMock = $this->getMock('\\Pinq\\Parsing\\IFunctionToExpressionTreeConverter');
        $queryableMock = $this->getMock('\\Pinq\\IQueryable');
        $queryProviderMock = $this->getMockBuilder('\\Pinq\\Providers\\QueryProvider')
                ->setMethods(['getFunctionToExpressionTreeConverter', 'createQueryable'])
                ->getMockForAbstractClass();
        
        $queryProviderMock
                ->expects($this->any())
                ->method('getFunctionToExpressionTreeConverter')
                ->will($this->returnValue($functionExpressionTreeConverterMock));
        
        $queryProviderMock
                ->expects($this->any())
                ->method('createQueryable')
                ->will($this->returnValue($queryableMock));
        
        $cachingProvider = new \Pinq\Providers\Caching\Provider($queryProviderMock);
        
        $this->assertSame($functionExpressionTreeConverterMock, $cachingProvider->getFunctionToExpressionTreeConverter());
        $this->assertSame($queryableMock, $cachingProvider->createQueryable());
        $this->assertSame($queryableMock, $cachingProvider->createQueryable(new Queries\Scope([])));
    }
    
    public function testThatLoadWillCacheTheEquivalentScopes()
    {
        $values = new \ArrayIterator(range(1, 10));
        
        $requestEvaluatorMock = $this->getMockBuilder('\\Pinq\\Queries\\Requests\\RequestVisitor')
                ->setMethods(['visitValues'])
                ->getMock();
        
        $requestEvaluatorMock
                ->expects($this->exactly(2))
                ->method('visitValues')
                ->will($this->returnValue($values));
        
        $queryProviderMock = $this->getMockForAbstractClass('\\Pinq\\Providers\\QueryProvider');

        $queryProviderMock
                ->expects($this->once())
                ->method('loadRequestEvaluatorVisitor')
                ->will($this->returnValue($requestEvaluatorMock));
        
        $cachingProvider = new \Pinq\Providers\Caching\Provider($queryProviderMock);
        
        //Load values request, empty
        $unscopedValues1 = $cachingProvider->load(new Queries\RequestQuery(new Queries\Scope([]), new Requests\Values()));
        
        $this->assertSame($values, $unscopedValues1);
        
        //Should not call loadRequestEvaluator again, scopes are equivalent
        $unscopedValues2 = $cachingProvider->load(new Queries\RequestQuery(new Queries\Scope([]), new Requests\Values()));
        
        $this->assertSame($values, $unscopedValues2);
    }
}
