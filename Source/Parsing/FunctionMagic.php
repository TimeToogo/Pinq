<?php

namespace Pinq\Parsing;

/**
 * Implementation of the function magic interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionMagic implements IFunctionMagic
{
    /**
     * @var IMagicConstants
     */
    private $constants;

    /**
     * @var IMagicScopes
     */
    private $scopes;

    public function __construct(IMagicConstants $constants, IMagicScopes $scopes)
    {
        $this->constants = $constants;
        $this->scopes    = $scopes;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function getScopes()
    {
        return $this->scopes;
    }
}
