<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Interface containing the structural information of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionStructure extends IMagicResolvable
{
    /**
     * Gets the function declaration structure.
     *
     * @return IFunctionDeclaration
     */
    public function getDeclaration();

    /**
     * Gets the body expressions of the function.
     *
     * @return O\Expression[]
     */
    public function getBodyExpressions();

    /**
     * {@inheritDoc}
     * @return IFunctionStructure
     */
    public function resolveMagic(IFunctionMagic $functionMagic);
}
