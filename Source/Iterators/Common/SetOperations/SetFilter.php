<?php

namespace Pinq\Iterators\Common\SetOperations;

use Pinq\Iterators\IIteratorScheme;
use Pinq\Iterators\ISet;

/**
 * Removes duplicate values
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class SetFilter implements ISetFilter
{
    /**
     * @var IIteratorScheme
     */
    protected $scheme;

    /**
     * @var ISet
     */
    protected $set;

    public function __construct(IIteratorScheme $scheme)
    {
        $this->scheme = $scheme;
    }
}
