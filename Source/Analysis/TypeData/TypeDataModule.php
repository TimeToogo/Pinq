<?php

namespace Pinq\Analysis\TypeData;

use Pinq\Analysis\PhpTypeSystem;

/**
 * Implementation of the type data module.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TypeDataModule implements ITypeDataModule
{
    const TYPE_SELF = PhpTypeSystem::TYPE_SELF;

    /**
     * @var array
     */
    private $types;

    /**
     * @var array
     */
    private $functions;

    public function __construct(array $types = [], array $functions = [])
    {
        $this->types = $types;
        $this->functions = $functions;
    }

    public static function getType()
    {
        return get_called_class();
    }

    public function types()
    {
        return $this->types;
    }

    public function functions()
    {
        return $this->functions;
    }
}
