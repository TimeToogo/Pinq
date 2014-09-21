<?php

namespace Pinq\Queries\Requests;

use Pinq\Queries\IRequest;

/**
 * Base class for a request query
 * Currently here for convenient namespacing
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Request implements IRequest
{
    public function getParameters()
    {
        return [];
    }
}
