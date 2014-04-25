<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Queries;

class ScopeTest extends \Pinq\Tests\PinqTestCase
{
    public function queryables()
    {
        return [
            [(new \Pinq\Traversable())->asQueryable()],
            [(new \Pinq\Traversable())->asRepository()],
        ];
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsTrueForSubscopedQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; })->asQueryable();
        $subscopedQueryable = $scopedQueryable->slice(0, 10)->asQueryable();
        
        $this->assertTrue($subscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsTrueForMatchingSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $function = function ($i) { return $i > 5; };
        $scopedQueryable = $queryable->where($function)->asQueryable();
        $subscopedQueryable = $queryable->where($function)->slice(0, 10)->asQueryable();
        
        $this->assertTrue($subscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsFalseForEqualSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $function = function ($i) { return $i > 5; };
        $scopedQueryable = $queryable->where($function)->asQueryable();
        $nonSubscopedQueryable = $queryable->where($function)->asQueryable();
        
        $this->assertFalse($nonSubscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsFalseForNonSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; })->asQueryable();
        $nonSubscopedQueryable = $queryable->where(function ($i) { return $i < 5; })->asQueryable();
        
        $this->assertFalse($nonSubscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     * @expectedException Pinq\PinqException
     */
    public function testThatGetSubscopeThrowsExceptionForNonSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; })->asQueryable();
        $nonSubscopedQueryable = $queryable->where(function ($i) { return $i < 5; })->asQueryable();
        
        $nonSubscopedQueryable->getScope()->getSubscopeOf($scopedQueryable->getScope());
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatGetSubscopeReturnsTheCorrectScopeForSubscopedQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; })->asQueryable();
        $subscopedQueryable = $scopedQueryable->slice(0, 10)->asQueryable();
        
        $this->assertEquals(
                $subscopedQueryable->getScope()->getSubscopeOf($scopedQueryable->getScope()),
                new Queries\Scope([new Queries\Segments\Range(0, 10)]));
    }
}
