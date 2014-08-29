<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Providers;
use Pinq\Providers\DSL\Compilation;
use Pinq\Queries;
use Pinq\Tests\PinqTestCase;

class DSLProviderTest extends PinqTestCase
{
    public function testRequestCallsLoadsAndCachesCompiledRequest()
    {
        $requestTemplateMock = $this->getMockForAbstractClass('Pinq\\Providers\\DSL\\Compilation\\IRequestTemplate');
        $requestTemplateMock
                ->expects($this->once())
                ->method('resolveStructuralParameters')
                ->will($this->returnValue(Compilation\Parameters\ResolvedParameterRegistry::none()));
        $compiledRequestMock = $this->getMockForAbstractClass('Pinq\\Providers\\DSL\\Compilation\\ICompiledRequest');;

        $cacheMock = $this->getMockForAbstractClass('Pinq\\Caching\\CacheAdapter');
        $cacheMock->expects($this->any())
                ->method('tryGet')
                ->will($this->returnValue(null));
        $cacheMock->expects($this->exactly(2))
                ->method('save')
                ->with(
                        $this->isType('string'),
                        $this->logicalOr(
                                $this->identicalTo($requestTemplateMock),
                                $this->identicalTo($compiledRequestMock)
                        )
                )
                ->will($this->returnValue(null));

        $configurationMock = $this->getMockBuilder('Pinq\\Providers\\DSL\\QueryCompilerConfiguration')
                ->setMethods(['buildCompiledQueryCache', 'createRequestTemplate', 'compileRequestQuery'])
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
        $configurationMock->expects($this->once())
                ->method('buildCompiledQueryCache')
                ->will($this->returnValue($cacheMock));
        $configurationMock->expects($this->once())
                ->method('createRequestTemplate')
                ->will($this->returnValue($requestTemplateMock));
        $configurationMock->expects($this->once())
                ->method('compileRequestQuery')
                ->will($this->returnValue($compiledRequestMock));
        $configurationMock->__construct();

        /** @var $provider \Pinq\Providers\DSL\QueryProvider|\PHPUnit_Framework_MockObject_MockObject */
        $provider = $this->getMockForAbstractClass(
                'Pinq\\Providers\\DSL\\QueryProvider',
                [new Queries\SourceInfo(''), $configurationMock]
        );

        $provider->expects($this->once())
                ->method('loadCompiledRequest')
                ->with($this->identicalTo($compiledRequestMock))
                ->will($this->returnValue([1, 2, 3]));

        $queryable = $provider->createQueryable();

        $queryable->getIterator();
    }

    public function testOperationCallsLoadsAndCachesCompiledRequest()
    {
        $operationTemplateMock = $this->getMockForAbstractClass(
                'Pinq\\Providers\\DSL\\Compilation\\IOperationTemplate'
        );
        $operationTemplateMock
                ->expects($this->once())
                ->method('resolveStructuralParameters')
                ->will($this->returnValue(Compilation\Parameters\ResolvedParameterRegistry::none()));
        $compiledOperationMock = $this->getMockForAbstractClass(
                'Pinq\\Providers\\DSL\\Compilation\\ICompiledOperation'
        );

        $cacheMock = $this->getMockForAbstractClass('Pinq\\Caching\\CacheAdapter');
        $cacheMock->expects($this->any())
                ->method('tryGet')
                ->will($this->returnValue(null));
        $cacheMock->expects($this->exactly(2))
                ->method('save')
                ->with(
                        $this->isType('string'),
                        $this->logicalOr(
                                $this->identicalTo($operationTemplateMock),
                                $this->identicalTo($compiledOperationMock)
                        )
                )
                ->will($this->returnValue(null));

        $configurationMock = $this->getMockBuilder('Pinq\\Providers\\DSL\\RepositoryCompilerConfiguration')
                ->setMethods(['buildCompiledQueryCache', 'createOperationTemplate', 'compileOperationQuery'])
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
        $configurationMock->expects($this->once())
                ->method('buildCompiledQueryCache')
                ->will($this->returnValue($cacheMock));
        $configurationMock->expects($this->once())
                ->method('createOperationTemplate')
                ->will($this->returnValue($operationTemplateMock));
        $configurationMock->expects($this->once())
                ->method('compileOperationQuery')
                ->will($this->returnValue($compiledOperationMock));
        $configurationMock->__construct();

        /** @var $provider \Pinq\Providers\DSL\RepositoryProvider|\PHPUnit_Framework_MockObject_MockObject */
        $provider = $this->getMockForAbstractClass(
                'Pinq\\Providers\\DSL\\RepositoryProvider',
                [
                        new Queries\SourceInfo(''),
                        $configurationMock,
                        $this->getMockBuilder('Pinq\\Providers\\DSL\\QueryProvider')->disableOriginalConstructor()
                                ->getMockForAbstractClass()
                ]
        );

        $provider->expects($this->once())
                ->method('executeCompiledOperation')
                ->with($this->identicalTo($compiledOperationMock));

        $repository = $provider->createRepository();

        $repository->clear();
    }
}
