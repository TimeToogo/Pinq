<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;


/**
 * Interface of the interpreter configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IInterpreterConfiguration
{
    /**
     * @return IRequestQueryInterpreter
     */
    public function buildRequestQueryInterpreter();
    public function buildOperationQueryInterpreter();
    public function buildScopeInterpreter();
    public function buildSourceInterpreter();
}