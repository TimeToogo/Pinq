<?php

namespace Pinq\Queries\Common\Join\Filter;

use Pinq\Expressions as O;
use Pinq\Queries\Common\Join\IFilter;
use Pinq\Queries\Functions;

/**
 * Custom join filter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Custom implements IFilter
{
    /**
     * The join filter connector function.
     *
     * @var Functions\ConnectorProjection
     */
    private $onFunction;

    public function __construct(Functions\ConnectorProjection $onFunction)
    {
        $this->onFunction = $onFunction;
    }

    public function getType()
    {
        return self::CUSTOM;
    }

    public function getParameters()
    {
        return $this->onFunction->getParameterIds();
    }

    /**
     * @return Functions\ConnectorProjection
     */
    public function getOnFunction()
    {
        return $this->onFunction;
    }

    /**
     * @param Functions\ConnectorProjection $onFunction
     *
     * @return Custom
     */
    public function update(Functions\ConnectorProjection $onFunction)
    {
        if ($this->onFunction === $onFunction) {
            return $this;
        }

        return new self($onFunction);
    }

    public function walk(O\ExpressionWalker $walker)
    {
        return $this->update($this->onFunction->walk($walker));
    }
}
