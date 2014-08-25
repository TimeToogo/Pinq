<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;

/**
 * Implementation of the structural expression parameter collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionCollection extends StructuralExpressionCollectionBase
{
    public function __construct()
    {
        parent::__construct(new \SplObjectStorage());
    }

    /**
     * @param IStructuralExpressionProcessor $processor
     *
     * @return ExpressionRegistry
     */
    public function getExpressions(IStructuralExpressionProcessor $processor)
    {
        return parent::getExpressions($processor);
    }

    /**
     * @param IStructuralExpressionProcessor $processor
     * @param ExpressionRegistry             $structuralExpressions
     *
     * @return void
     */
    public function add(IStructuralExpressionProcessor $processor, ExpressionRegistry $structuralExpressions)
    {
        $this->structuralExpressions[$processor] = $structuralExpressions;
    }

    /**
     * @return StructuralExpressionRegistry
     */
    public function buildRegistry()
    {
        return new StructuralExpressionRegistry($this->structuralExpressions);
    }
}