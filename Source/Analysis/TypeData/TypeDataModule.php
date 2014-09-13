<?php

namespace Pinq\Analysis\TypeData;

use Pinq\Analysis\PhpTypeSystem;

/**
 * Base class for type data modules.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class TypeDataModule
{
    const TYPE_SELF = PhpTypeSystem::TYPE_SELF;

    public function __construct()
    {

    }

    public static function getType()
    {
        return get_called_class();
    }

    public function functions()
    {
        return [];
    }

    public function types()
    {
        return [];
    }
} 