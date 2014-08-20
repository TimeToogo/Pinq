<?php

namespace Pinq\Providers\Traversable;

use Pinq\ITraversable;
use Pinq\Queries;

/**
 * Source info class for traversable queries.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SourceInfo extends Queries\SourceInfo
{
    const SOURCE_INFO_TYPE = __CLASS__;

    /**
     * @var ITraversable
     */
    protected $traversable;

    public function __construct(ITraversable $traversable)
    {
        parent::__construct(spl_object_hash($traversable));
        $this->traversable = $traversable;
    }

    /**
     * @return \Pinq\ITraversable
     */
    public function getTraversable()
    {
        return $this->traversable;
    }
}
