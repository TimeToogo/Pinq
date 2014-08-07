<?php

namespace Pinq\Queries\Common\Join;

use Pinq\Queries\Common;

/**
 * Class containing common join options
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Options
{
    /**
     * The values to join.
     *
     * @var Common\ISource
     */
    protected $source;

    /**
     * @var boolean
     */
    protected $hasDefault = false;

    /**
     * @var string
     */
    protected $defaultValueId;

    /**
     * @var string
     */
    protected $defaultKeyId;

    /**
     * @var boolean
     */
    protected $isGroupJoin;

    /**
     * The join filter.
     *
     * @var IFilter|null
     */
    protected $filter;

    public function __construct(
            Common\ISource $source,
            $isGroupJoin,
            IFilter $filter = null,
            $hasDefault = false,
            $defaultValueId = null,
            $defaultKeyId = null
    ) {
        $this->source         = $source;
        $this->isGroupJoin    = $isGroupJoin;
        $this->filter         = $filter;
        $this->hasDefault     = $hasDefault;
        $this->defaultValueId = $defaultValueId;
        $this->defaultKeyId   = $defaultKeyId;
    }

    /**
     * @return Common\ISource
     */
    final public function getSource()
    {
        return $this->source;
    }

    /**
     * @return boolean
     */
    final public function isGroupJoin()
    {
        return $this->isGroupJoin;
    }

    /**
     * @return boolean
     */
    final public function hasFilter()
    {
        return $this->filter !== null;
    }

    /**
     * @return IFilter|null
     */
    final public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return boolean
     */
    final public function hasDefault()
    {
        return $this->hasDefault;
    }

    /**
     * @return string
     */
    final public function getDefaultValueId()
    {
        return $this->defaultValueId;
    }

    /**
     * @return string
     */
    final public function getDefaultKeyId()
    {
        return $this->defaultKeyId;
    }
}
