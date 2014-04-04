<?php

namespace Pinq\Providers;

use \Pinq\FunctionExpressionTree;

interface IQueryScope
{
    /**
     * @return \Pinq\Queries\IQueryStream
     */
    public function GetQueryStream();
    
    /**
     * @return \Traversable
     */
    public function GetValues();
    
    /**
     * @return mixed
     */
    public function First();
    
    /**
     * @return int
     */
    public function Count();
    
    /**
     * @return boolean
     */
    public function Exists();
    
    /**
     * @param mixed $Value 
     * @return boolean
     */
    public function Contains($Value);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return mixed
     */
    public function Aggregate(FunctionExpressionTree $ExpressionTree);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return mixed
     */
    public function Maximum(FunctionExpressionTree $ExpressionTree = null);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return mixed
     */
    public function Minimum(FunctionExpressionTree $ExpressionTree = null);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return int|null
     */
    public function Sum(FunctionExpressionTree $ExpressionTree = null);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return int|null
     */
    public function Average(FunctionExpressionTree $ExpressionTree = null);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return bool
     */
    public function All(FunctionExpressionTree $ExpressionTree = null);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return bool
     */
    public function Any(FunctionExpressionTree $ExpressionTree = null);
    
    /**
     * @param FunctionExpressionTree $ExpressionTree
     * @return string
     */
    public function Implode($Delimiter, FunctionExpressionTree $ExpressionTree = null);
}
