<?php

namespace Pinq\Queries\Operations;

/**
 * Base class for operations.
 * Currently here just for convenient namespace usage
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Operation implements \Pinq\Queries\IOperation
{
    public function getParameters()
    {
        return [];
    }
}
