<?php

namespace Pinq\Queries\Common\Join\Filter;

use Pinq\Queries\Common\Join\IFilter;
use Pinq\Queries\Functions;

/**
 * Equality join filter.
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
}
