<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Queries;

/**
 * Factory class to build a query processor from the supplied query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ProcessorFactory
{
    /**
     * Builds a query processor from the supplied query.
     *
     * @param Queries\IQuery       $query
     * @param IExpressionProcessor $expressionProcessor
     *
     * @return OperationQueryProcessor|RequestQueryProcessor
     */
    public static function from(Queries\IQuery $query, IExpressionProcessor $expressionProcessor)
    {
        if ($query instanceof Queries\IRequestQuery) {
            return new RequestQueryProcessor($expressionProcessor, $query);
        } elseif ($query instanceof Queries\IOperationQuery) {
            return new OperationQueryProcessor($expressionProcessor, $query);
        }
    }
}
