<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Queries;

class ScopeTest extends \Pinq\Tests\PinqTestCase
{
    public function queryables()
    {
        return [
            [(new \Pinq\Providers\Traversable\Provider(new \Pinq\Traversable()))->createQueryable()],
            [(new \Pinq\Providers\Collection\Provider(new \Pinq\Collection()))->createRepository()],
        ];
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsTrueForSubscopedQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; });
        $subscopedQueryable = $scopedQueryable->slice(0, 10);
        
        $this->assertTrue($subscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsTrueForMatchingSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $function = function ($i) { return $i > 5; };
        $scopedQueryable = $queryable->where($function);
        $subscopedQueryable = $queryable->where($function)->slice(0, 10);
        
        $this->assertTrue($subscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsFalseForEqualSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $function = function ($i) { return $i > 5; };
        $scopedQueryable = $queryable->where($function);
        $nonSubscopedQueryable = $queryable->where($function);
        
        $this->assertFalse($nonSubscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatSubscopeReturnsFalseForNonSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; });
        $nonSubscopedQueryable = $queryable->where(function ($i) { return $i < 5; });
        
        $this->assertFalse($nonSubscopedQueryable->getScope()->isSubscopeOf($scopedQueryable->getScope()));
    }
    
    /**
     * @dataProvider queryables
     * @expectedException Pinq\PinqException
     */
    public function testThatGetSubscopeThrowsExceptionForNonSubscopeQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; });
        $nonSubscopedQueryable = $queryable->where(function ($i) { return $i < 5; });
        
        $nonSubscopedQueryable->getScope()->getSubscopeOf($scopedQueryable->getScope());
    }
    
    /**
     * @dataProvider queryables
     */
    public function testThatGetSubscopeReturnsTheCorrectScopeForSubscopedQueries(\Pinq\IQueryable $queryable)
    {
        $scopedQueryable = $queryable->where(function ($i) { return $i > 5; });
        $subscopedQueryable = $scopedQueryable->slice(0, 10);
        
        $this->assertEquals(
                $subscopedQueryable->getScope()->getSubscopeOf($scopedQueryable->getScope()),
                new Queries\Scope([new Queries\Segments\Range(0, 10)]));
    }
}
