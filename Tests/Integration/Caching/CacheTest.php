<?php

namespace Pinq\Tests\Integration\Caching;

use \Pinq\Expressions as O;
use \Pinq\Caching\IFunctionCache;

abstract class CacheTest extends \Pinq\Tests\PinqTestCase
{
    protected static $RootCacheDirectory; 
    
    /**
     * @var IFunctionCache 
     */
    protected $Cache;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        
        self::$RootCacheDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'CacheFiles' . DIRECTORY_SEPARATOR;
        if(!is_dir(self::$RootCacheDirectory)) {
            mkdir(self::$RootCacheDirectory, 0777, true);
        }
    }
    
    public function testThatCacheSavesAndRetrievesExpressionTree()
    {
        $Hash = __METHOD__;
        $ExpressionTree = new \Pinq\FunctionExpressionTree(
                null, 
                [O\Expression::Parameter('I')], 
                [O\Expression::ReturnExpression(
                        O\Expression::BinaryOperation(
                                O\Expression::Variable(O\Expression::Value('I')), 
                                O\Operators\Binary::Addition, 
                                O\Expression::Value(2)))]);
        
        $this->AssertThatIsCachedCorrectrly($Hash, $ExpressionTree);
    }
    
    public function testThatCacheSavesAndRetrievesBlankExpressionTree()
    {
        $Hash = __METHOD__;
        $BlankExpressionTree = new \Pinq\FunctionExpressionTree(null, [], []);
        
        $this->AssertThatIsCachedCorrectrly($Hash, $BlankExpressionTree);
    }
    
    final protected function AssertThatIsCachedCorrectrly($Hash, \Pinq\FunctionExpressionTree $ExpressionTree)
    {
        if($this->Cache === null) {
            throw new \Exception('Please set the Cache field');
        }
        
        $this->Cache->Save($Hash, $ExpressionTree);
        
        $RetrievedExpressionTree = $this->Cache->TryGet($Hash);
        
        $this->assertInstanceOf('\Pinq\FunctionExpressionTree', $RetrievedExpressionTree, 'The cache should return am expression tree and not null');
        $this->assertNotSame($ExpressionTree, $RetrievedExpressionTree, 'The cache should return a clone and not the same instance');
        $this->assertEquals($ExpressionTree, $RetrievedExpressionTree, 'The cache should not alter the expression tree');
    }
    
    public function testThatTryingToGetNonExistentExpressionTreeReturnsNull()
    {
        $this->assertNull($this->Cache->TryGet(__METHOD__));
    }
    
    public function testThatRemovedExpressionTreeReturnsNull()
    {
        $Hash1 = __METHOD__ . '1';
        $Hash2 = __METHOD__ . '2';
        $BlankExpressionTree = new \Pinq\FunctionExpressionTree(null, [], []);
        
        $this->Cache->Save($Hash1, $BlankExpressionTree);
        $this->Cache->Save($Hash2, $BlankExpressionTree);
        
        $this->Cache->Remove($Hash1);
        
        $this->assertNull($this->Cache->TryGet($Hash1));
        $this->assertEquals($BlankExpressionTree, $this->Cache->TryGet($Hash2));
    }
    
    public function testThatClearedCacheRemovesSavedExpressionTrees()
    {
        $Hash1 = __METHOD__ . '1';
        $Hash2 = __METHOD__ . '2';
        $BlankExpressionTree = new \Pinq\FunctionExpressionTree(null, [], []);
        
        $this->Cache->Save($Hash1, $BlankExpressionTree);
        $this->Cache->Save($Hash2, $BlankExpressionTree);
        
        $this->Cache->Clear();
        
        $this->assertNull($this->Cache->TryGet($Hash1));
        $this->assertNull($this->Cache->TryGet($Hash2));
    }
}
