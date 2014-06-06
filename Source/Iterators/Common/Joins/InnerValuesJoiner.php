<?php

namespace Pinq\Iterators\Common\Joins;

use Pinq\Iterators\Common;
use Pinq\Iterators\IIteratorScheme;

/**
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class InnerValuesJoiner implements IInnerValuesJoiner
{
    /**
     * @var IIteratorScheme
     */
    protected $scheme;

    public function __construct(IIteratorScheme $scheme)
    {
        $this->scheme = $scheme;
    }
}
