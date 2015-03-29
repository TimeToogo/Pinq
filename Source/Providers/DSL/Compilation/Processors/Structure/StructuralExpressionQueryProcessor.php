<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Providers\DSL\Compilation\Parameters\ParameterCollectionBase;
use Pinq\Providers\DSL\Compilation\Processors\Expression\ExpressionProcessor;
use Pinq\Queries;

/**
 * Base class of the structural expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class StructuralExpressionQueryProcessor extends ExpressionProcessor
{
    /**
     * @var ParameterCollectionBase
     */
    protected $parameters;

    /**
     * @var IStructuralExpressionProcessor
     */
    protected $processor;

    public function __construct(
            ParameterCollectionBase $parameters,
            IStructuralExpressionProcessor $processor
    ) {
        $this->parameters = $parameters;
        $this->processor  = $processor;
    }
}
