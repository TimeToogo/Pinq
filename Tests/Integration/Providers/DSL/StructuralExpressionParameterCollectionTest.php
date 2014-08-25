<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionRegistry;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;
use Pinq\Providers\DSL\Compilation\Parameters\StructuralExpressionCollection;
use Pinq\Providers\DSL\Compilation\Parameters\ExpressionParameter;
use Pinq\Queries\Functions;
use Pinq\Queries\ResolvedParameterRegistry;
use Pinq\Tests\PinqTestCase;

class StructuralExpressionParameterCollectionTest extends PinqTestCase
{
    /**
     * @var IStructuralExpressionProcessor
     */
    protected $processor;

    /**
     * @var StructuralExpressionCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->processor  = $this->getMock('Pinq\\Providers\\DSL\\Compilation\\Processors\\Structure\\IStructuralExpressionProcessor');
        $this->collection = new StructuralExpressionCollection();
    }

    public function testCollectionAddsStructuralExpressionsCorrectlyForProcessor()
    {
        $this->collection->add($this->processor, $registry = ExpressionRegistry::none());

        $this->assertSame($registry, $this->collection->getExpressions($this->processor));
        $this->assertSame([$this->processor], $this->collection->getProcessors());
        $this->assertSame(1, $this->collection->countProcessors());
        $this->assertSame(0, $this->collection->countExpressions());
    }

    public function testCollectionAddsStructuralExpressionsCorrectlyForProcessorAndBuildsRegistryCorrectly()
    {
        $this->collection->add($this->processor, $registry = ExpressionRegistry::none());
        $structuralExpressionRegistry = $this->collection->buildRegistry();

        $this->assertSame($registry, $structuralExpressionRegistry->getExpressions($this->processor));
        $this->assertSame([$this->processor], $structuralExpressionRegistry->getProcessors());
        $this->assertSame(1, $structuralExpressionRegistry->countProcessors());
        $this->assertSame(0, $structuralExpressionRegistry->countExpressions());
    }
}
 