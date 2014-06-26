<?php

namespace Pinq\Queries\Common\Join\Filter;

use Pinq\Queries\Common\Join\IFilter;
use Pinq\FunctionExpressionTree;

/**
 * Custom join filter.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class On implements IFilter
{
   /**
     * The join filter expression tree
     *
     * @var FunctionExpressionTree|null
     */
    private $onFunction;

    public function __construct(FunctionExpressionTree $onFunction)
    {
        $this->onFunction = $onFunction;
    }
    
    public function getType()
    {
        return self::ON;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getOnFunctionExpressionTree()
    {
        return $this->onFunction;
    }
}
