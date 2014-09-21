<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\IOperation;

/**
 * Base class for operations.
 * Currently here just for convenient namespace usage
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Operation implements IOperation
{
    public function getParameters()
    {
        return [];
    }
}
