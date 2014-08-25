<?php

namespace Pinq\Queries\Common\Join\Filter;

use Pinq\Expressions as O;
use Pinq\Queries\Common\Join\IFilter;
use Pinq\Queries\Functions;

/**
 * Equality join filter.
 * Matches on equality between the values from the inner and
 * outer projections except for null.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Equality implements IFilter
{
    /**
     * The outer key projection function.
     *
     * @var Functions\ElementProjection
     */
    private $outerKeyFunction;

    /**
     * The inner key projection function.
     *
     * @var Functions\ElementProjection
     */
    private $innerKeyFunction;

    public function __construct(
            Functions\ElementProjection $outerKeyFunction,
            Functions\ElementProjection $innerKeyFunction
    ) {
        $this->outerKeyFunction = $outerKeyFunction;
        $this->innerKeyFunction = $innerKeyFunction;
    }

    public function getType()
    {
        return self::EQUALITY;
    }

    public function getParameters()
    {
        return array_merge(
                $this->outerKeyFunction->getParameterIds(),
                $this->innerKeyFunction->getParameterIds()
        );
    }

    /**
     * @return Functions\ElementProjection
     */
    public function getOuterKeyFunction()
    {
        return $this->outerKeyFunction;
    }

    /**
     * @return Functions\ElementProjection
     */
    public function getInnerKeyFunction()
    {
        return $this->innerKeyFunction;
    }

    /**
     * @param Functions\ElementProjection $outerKeyFunction
     * @param Functions\ElementProjection $innerKeyFunction
     *
     * @return Equality
     */
    public function update(
            Functions\ElementProjection $outerKeyFunction,
            Functions\ElementProjection $innerKeyFunction
    ) {
        if ($this->outerKeyFunction === $outerKeyFunction
                && $this->innerKeyFunction === $innerKeyFunction
        ) {
            return $this;
        }

        return new self($outerKeyFunction, $innerKeyFunction);
    }

    public function walk(O\ExpressionWalker $walker)
    {
        return $this->update($this->outerKeyFunction->walk($walker), $this->innerKeyFunction->walk($walker));
    }
}
