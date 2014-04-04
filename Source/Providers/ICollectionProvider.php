<?php

namespace Pinq\Providers;

interface ICollectionProvider extends IQueryProvider
{
    /**
     * @param  FunctionExpressionTree $ExpressionTree
     * @return void
     */
    public function Apply(Queries\IQueryStream $QueryStream = null, FunctionExpressionTree $ExpressionTree);

    /**
     * @param  array|\Traversable $Values The values to add
     * @return void
     */
    public function AddRange(array $Values);

    /**
     * @param  array|\Traversable $Values The values to remove
     * @return void
     */
    public function RemoveRange(array $Values);

    /**
     * @param  FunctionExpressionTree $ExpressionTree
     * @return void
     */
    public function RemoveWhere(FunctionExpressionTree $ExpressionTree);

    /**
     * @return void
     */
    public function Clear();
}
