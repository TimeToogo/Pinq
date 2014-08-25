<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Providers\DSL\Compilation\Parameters\ExpressionCollectionBase;
use Pinq\Providers\DSL\Compilation\Processors\Expression\ExpressionProcessor;
use Pinq\Queries;

/**
 * Base class of the structural expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class StructuralExpressionProcessor extends ExpressionProcessor
{
    /**
     * @var ExpressionCollectionBase
     */
    protected $expressions;

    /**
     * @var IStructuralExpressionProcessor
     */
    protected $processor;

    public function __construct(
            ExpressionCollectionBase $expressions,
            IStructuralExpressionProcessor $processor,
            Queries\IScope $scope
    ) {
        parent::__construct($scope);
        $this->expressions = $expressions;
        $this->processor   = $processor;
    }
}