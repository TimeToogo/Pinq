<?php

namespace Pinq\Queries\Builders\Functions;

use Pinq\Queries;

/**
 * Base class of the function interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class BaseFunction implements IFunction
{
    /**
     * @var string
     */
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    final public function getId()
    {
        return $this->id;
    }
}
