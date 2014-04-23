<?php 

namespace Pinq\Tests\Integration\Caching;

use Pinq\Expressions as O;
use Pinq\Caching\IFunctionCache;

abstract class CacheTest extends \Pinq\Tests\PinqTestCase
{
    protected static $rootCacheDirectory;
    
    /**
     * @var IFunctionCache 
     */
    protected $cache;
    
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$rootCacheDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'CacheFiles' . DIRECTORY_SEPARATOR;
        
        if (!is_dir(self::$rootCacheDirectory)) {
            mkdir(self::$rootCacheDirectory, 511, true);
        }
    }
    
    public function testThatCacheSavesAndRetrievesExpressionTree()
    {
        $hash = __METHOD__;
        $expressionTree = 
                new \Pinq\FunctionExpressionTree(
                        null,
                        [O\Expression::parameter('I')],
                        [O\Expression::returnExpression(O\Expression::binaryOperation(
                                O\Expression::variable(O\Expression::value('I')),
                                O\Operators\Binary::ADDITION,
                                O\Expression::value(2)))]);
        
        $this->assertThatIsCachedCorrectrly($hash, $expressionTree);
    }
    
    public function testThatCacheSavesAndRetrievesBlankExpressionTree()
    {
        $hash = __METHOD__;
        $blankExpressionTree = new \Pinq\FunctionExpressionTree(null, [], []);
        
        $this->assertThatIsCachedCorrectrly($hash, $blankExpressionTree);
    }
    
    protected final function assertThatIsCachedCorrectrly($hash, \Pinq\FunctionExpressionTree $expressionTree)
    {
        if ($this->cache === null) {
            throw new \Exception('Please set the Cache field');
        }
        
        $this->cache->save($hash, $expressionTree);
        $retrievedExpressionTree = $this->cache->tryGet($hash);
        
        $this->assertInstanceOf(
                '\\Pinq\\FunctionExpressionTree',
                $retrievedExpressionTree,
                'The cache should return am expression tree and not null');
        
        $this->assertNotSame(
                $expressionTree,
                $retrievedExpressionTree,
                'The cache should return a clone and not the same instance');
        
        $this->assertEquals(
                $expressionTree,
                $retrievedExpressionTree,
                'The cache should not alter the expression tree');
    }
    
    public function testThatTryingToGetNonExistentExpressionTreeReturnsNull()
    {
        $this->assertNull($this->cache->tryGet(__METHOD__));
    }
    
    public function testThatRemovedExpressionTreeReturnsNull()
    {
        $hash1 = __METHOD__ . '1';
        $hash2 = __METHOD__ . '2';
        
        $blankExpressionTree = new \Pinq\FunctionExpressionTree(null, [], []);
        $this->cache->save($hash1, $blankExpressionTree);
        $this->cache->save($hash2, $blankExpressionTree);
        $this->cache->remove($hash1);
        
        $this->assertNull($this->cache->tryGet($hash1));
        
        $this->assertEquals(
                $blankExpressionTree,
                $this->cache->tryGet($hash2));
    }
    
    public function testThatClearedCacheRemovesSavedExpressionTrees()
    {
        $hash1 = __METHOD__ . '1';
        $hash2 = __METHOD__ . '2';
        
        $blankExpressionTree = new \Pinq\FunctionExpressionTree(null, [], []);
        $this->cache->save($hash1, $blankExpressionTree);
        $this->cache->save($hash2, $blankExpressionTree);
        $this->cache->clear();
        
        $this->assertNull($this->cache->tryGet($hash1));
        $this->assertNull($this->cache->tryGet($hash2));
    }
}