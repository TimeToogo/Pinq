<?php

namespace Pinq\Queries\Common\Join\Filter;

use Pinq\Queries\Common\Join\IFilter;
use Pinq\FunctionExpressionTree;

/**
 * Equality join filter.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Equality implements IFilter
{
    /**
     * The outer key selector function
     *
     * @var FunctionExpressionTree
     */
    private $outerKeyFunction;

    /**
     * The inner key selector function
     *
     * @var FunctionExpressionTree
     */
    private $innerKeyFunction;

    public function __construct(FunctionExpressionTree $outerKeyFunction, FunctionExpressionTree $innerKeyFunction)
    {
        $this->outerKeyFunction = $outerKeyFunction;
        $this->innerKeyFunction = $innerKeyFunction;
    }
    
    public function getType()
    {
        return self::EQUALITY;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getOuterKeyFunctionExpressionTree()
    {
        return $this->outerKeyFunction;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getInnerKeyFunctionExpressionTree()
    {
        return $this->innerKeyFunction;
    }
}
