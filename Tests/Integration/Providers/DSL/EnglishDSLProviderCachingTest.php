<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\IRepository;
use Pinq\Providers\DSL\Compilation\StaticOperationTemplate;
use Pinq\Providers\DSL\Compilation\StaticRequestTemplate;
use Pinq\Queries;
use Pinq\Queries\ParameterRegistry;
use Pinq\Tests\Integration\Providers\DSL\Implementation\English\CompiledQuery;
use Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors\StructuralVariableProcessor;

class EnglishDSLProviderCachingTest extends DSLCompilationProviderTest
{
    protected function compilerConfiguration()
    {
        return new Implementation\English\Configuration();
    }

    protected function structuralExpressionProcessors()
    {
        return [new StructuralVariableProcessor()];
    }

    protected function assertQueryCompiledCorrectly($compiledQuery, $correctValue)
    {
        $this->assertCachedEquals($correctValue);
    }

    protected function assertCachedEquals(array $correctValues)
    {
        $cachedArray = $this->compiledQueryCache->getCachedArray();
        $this->assertEqualsButIgnoreParameterIds($correctValues, array_values($cachedArray));
    }

    protected function assertInCache($value)
    {
        $cachedArray = $this->compiledQueryCache->getCachedArray();
        $this->assertContainsEquals($value, $cachedArray, '', false, false);//Only check equality
    }

    protected function assertContainsInstanceofInCache($type)
    {
        $contains = false;
        foreach($this->compiledQueryCache->getCachedArray() as $cachedValue) {
            if($cachedValue instanceof $type) {
                $contains = true;
            }
        }

        $this->assertTrue($contains, 'Cache must contain an instance of ' . $type);
    }

    /**
     * @dataProvider allImplementations
     */
    public function testStaticRequestTemplateIsCached()
    {
        $this->assertRequestIsCorrect(
                function (IQueryable $queryable) {
                    $queryable->sum();
                },
                [
                        new StaticRequestTemplate(
                                new ParameterRegistry([]),
                                new CompiledQuery(<<<'ENG'
Get the sum of the values
ENG
                                ))
                ],
                true
        );
    }

    /**
     * @dataProvider repositories
     */
    public function testStaticOperationTemplateIsCached()
    {
        $this->assertOperationIsCorrect(
                function (IRepository $repository) {
                    $repository->clear();
                },
                [
                        new StaticOperationTemplate(
                                new ParameterRegistry([]),
                                new CompiledQuery(<<<'ENG'
Remove all the elements
ENG
                                ))
                ]
        );
    }

    /**
     * @dataProvider allImplementations
     */
    public function testRequestTemplateWithStructuralParametersAllCompiledQueriesIsCached()
    {
        foreach (['i', 'o'] as $varName) {
            $this->loadCompiledRequestQuery(
                    $this->queryable
                            ->where(function ($i, $o) use ($varName) { return $$varName; })
                            ->getExpression()
            );
        }

        //Should contain request template + 2 compiled queries
        $this->assertCount(3, $this->compiledQueryCache->getCachedArray());
        $this->assertInCache(
                new CompiledQuery(<<<'ENG'
Filter according to: { return $i; } with parameters: [$this, $varName]
Get the elements as itself
ENG
                )
        );
        $this->assertInCache(
                new CompiledQuery(<<<'ENG'
Filter according to: { return $o; } with parameters: [$this, $varName]
Get the elements as itself
ENG
                )
        );
        $this->assertContainsInstanceofInCache('Pinq\\Providers\\DSL\\Compilation\\IRequestTemplate');
    }

    /**
     * @dataProvider allImplementations
     */
    public function testOperationTemplateWithStructuralParametersAllCompiledQueriesIsCached()
    {
        foreach (['abcdef', 'qwerty123', 'bbvvcc'] as $varName) {
            $expression = $this->parseQueryExpression(function (IRepository $repository) use ($varName) {
                        $repository
                                ->take(5)
                                ->join([])
                                ->apply(function (&$i, $o) use ($varName) { $i /= $$varName; });
                    }, /* out */ $evaluationContext);

            $this->loadCompiledOperationQuery($expression, $evaluationContext);
        }

        //Should contain operation template + 3 compiled queries
        $this->assertCount(4, $this->compiledQueryCache->getCachedArray());
        $this->assertInCache(
                new CompiledQuery(<<<'ENG'
Starting from and up to the specified element
Join with: [array or iterator] and update the outer values according to: { $i /= $abcdef; } with parameters: [$this, $varName]
ENG
                )
        );
        $this->assertInCache(
                new CompiledQuery(<<<'ENG'
Starting from and up to the specified element
Join with: [array or iterator] and update the outer values according to: { $i /= $qwerty123; } with parameters: [$this, $varName]
ENG
                )
        );
        $this->assertInCache(
                new CompiledQuery(<<<'ENG'
Starting from and up to the specified element
Join with: [array or iterator] and update the outer values according to: { $i /= $bbvvcc; } with parameters: [$this, $varName]
ENG
                )
        );
        $this->assertContainsInstanceofInCache('Pinq\\Providers\\DSL\\Compilation\\IOperationTemplate');
    }
}
