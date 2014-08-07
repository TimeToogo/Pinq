<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Interface containing the reflection data of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionSignature extends IMagicResolvable
{
    const TYPE_FUNCTION = 0;
    const TYPE_METHOD   = 1;
    const TYPE_CLOSURE  = 2;

    const ACCESS_PUBLIC    = 0;
    const ACCESS_PROTECTED = 1;
    const ACCESS_PRIVATE   = 2;

    const POLYMORPH_ABSTRACT = 0;
    const POLYMORPH_FINAL    = 1;

    /**
     * Gets the function type constant.
     *
     * @return int
     */
    public function getType();

    /**
     * Whether the function returns a reference.
     *
     * @return boolean
     */
    public function returnsReference();

    /**
     * Gets the access modifier constant.
     * Null for non method functions.
     *
     * @return int|null
     */
    public function getAccessModifier();

    /**
     * Gets the polymorph modifier constant.
     * Null for non method functions.
     *
     * @return int|null
     */
    public function getPolymorphModifier();

    /**
     * Whether the function is static.
     * Null for non method functions.
     *
     * @return boolean|null
     */
    public function isStatic();

    /**
     * Gets the name of the function/method.
     * Null for closures.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Gets the parameters expressions of the function.
     *
     * @return O\ParameterExpression[]
     */
    public function getParameterExpressions();

    /**
     * {@inheritDoc}
     * @return IFunctionSignature
     */
    public function resolveMagic(IFunctionMagic $functionMagic);

    /**
     * Gets an array of the scoped variable names.
     * Null for non closure functions.
     *
     * @return string[]|null
     */
    public function getScopedVariableNames();

    /**
     * Gets a unique hash of the function signature.
     *
     * @return string
     */
    public function getHash();
}
