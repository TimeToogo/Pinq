<?php

namespace Pinq\Parsing;

/**
 * Interface for a set of resolved self::, static::, parent:: scopes of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IMagicScopes
{
    /**
     * Gets the fully qualified class of self:: scope.
     * Null if not applicable.
     *
     * @return string|null
     */
    public function getSelfClass();

    /**
     * Gets the value of self::class constant.
     * Null if not applicable.
     *
     * @return string|null
     */
    public function getSelfClassConstant();

    /**
     * Gets the fully qualified class of static:: scope.
     * Null if not applicable.
     *
     * @return string|null
     */
    public function getStaticClass();

    /**
     * Gets the value of static::class constant.
     * Null if not applicable.
     *
     * @return string|null
     */
    public function getStaticClassConstant();

    /**
     * Gets the fully qualified class of parent:: scope.
     * Null if not applicable.
     *
     * @return string|null
     */
    public function getParentClass();

    /**
     * Gets the value of parent::class constant.
     * Null if not applicable.
     *
     * @return string|null
     */
    public function getParentClassConstant();
}
