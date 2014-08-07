<?php

namespace Pinq\Queries\Common\Join\Filter;

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

    /**
     * @return Functions\ConnectorProjection
     */
    public function getOnFunction()
    {
        return $this->onFunction;
    }
}
