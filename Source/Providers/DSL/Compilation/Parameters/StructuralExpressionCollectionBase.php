<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;

/**
 * Base class of the structural expression parameter collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionCollectionBase
{
    /**
     * @var \SplObjectStorage|ExpressionRegistry[]
     */
    protected $structuralExpressions;

    public function __construct(\SplObjectStorage $structuralExpressions)
    {
        $this->structuralExpressions = $structuralExpressions;
    }

    /**
     * @return IStructuralExpressionProcessor[]
     */
    public function getProcessors()
    {
        return iterator_to_array($this->structuralExpressions);
    }

    /**
     * @param IStructuralExpressionProcessor $processor
     *
     * @return ExpressionCollectionBase
     */
    public function getExpressions(IStructuralExpressionProcessor $processor)
    {
        return $this->structuralExpressions[$processor];
    }

    /**
     * @return int
     */
    public function countProcessors()
    {
        return $this->structuralExpressions->count();
    }

    /**
     * @return int
     */
    public function countExpressions()
    {
        $count = 0;
        foreach($this->structuralExpressions as $processor) {
            $count += $this->structuralExpressions[$processor]->count();
        }

        return $count;
    }
}