<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Providers\DSL\Compilation;
use Pinq\Providers;
use Pinq\Queries;
use Pinq\Tests\PinqTestCase;

class DSLProviderTest extends PinqTestCase
{
    protected function queryTemplateMock($queryTemplateClass)
    {
        $queryTemplateMock = $this->getMockForAbstractClass($queryTemplateClass);
        $queryTemplateMock
                ->expects($this->once())
                ->method('resolveStructuralParameters')
                ->will($this->returnValue(Compilation\Parameters\ResolvedParameterRegistry::none()));
        $queryTemplateMock
                ->expects($this->once())
                ->method('getParameters')
                ->will($this->returnValue(new Queries\ParameterRegistry([])));

        return $queryTemplateMock;
    }

    protected function queryCacheAdapterMock(
            Compilation\IQueryTemplate $queryTemplateMock,
            Compilation\ICompiledQuery $compiledQueryMock
    ) {
        $cacheMock = $this->getMockBuilder('Pinq\\Caching\\NullCache')
                ->disableOriginalConstructor()
                ->setMethods(['tryGet', 'save'])
                ->getMock();
        $cacheMock->expects($this->any())
                ->method('tryGet')
                ->will($this->returnValue(null));
        $cacheMock->expects($this->exactly(2))
                ->method('save')
                ->with(
                        $this->isType('string'),
                        $this->logicalOr(
                                $this->identicalTo($queryTemplateMock),
                                $this->identicalTo($compiledQueryMock)
                        )
                )
                ->will($this->returnValue(null));

        return $cacheMock;
    }

    public function testRequestCallsLoadsAndCachesCompiledRequest()
    {
        $requestTemplateMock = $this->queryTemplateMock('Pinq\\Providers\\DSL\\Compilation\\IRequestTemplate');
        $compiledRequestMock = $this->getMockForAbstractClass('Pinq\\Providers\\DSL\\Compilation\\ICompiledRequest');;

        $cacheMock = $this->queryCacheAdapterMock($requestTemplateMock, $compiledRequestMock);

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
                ->will($this->returnValue(new \Pinq\Traversable([1, 2, 3])));

        $queryable = $provider->createQueryable();

        //Perform request
        $queryable->getIterator();
    }

    public function testOperationCallsLoadsAndCachesCompiledRequest()
    {
        $operationTemplateMock = $this->queryTemplateMock('Pinq\\Providers\\DSL\\Compilation\\IOperationTemplate');
        $compiledOperationMock = $this->getMockForAbstractClass(
                'Pinq\\Providers\\DSL\\Compilation\\ICompiledOperation'
        );

        $cacheMock = $this->queryCacheAdapterMock($operationTemplateMock, $compiledOperationMock);

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

        //Perform operation
        $repository->clear();
    }
}
