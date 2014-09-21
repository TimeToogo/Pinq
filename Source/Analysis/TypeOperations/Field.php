<?php

namespace Pinq\Analysis\TypeOperations;

use Pinq\Analysis\IField;
use Pinq\Analysis\ITypeSystem;

/**
 * Implementation of the field.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Field extends TypeOperation implements IField
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $isStatic;

    public function __construct(ITypeSystem $typeSystem, $sourceType, $name, $isStatic, $returnType)
    {
        parent::__construct($typeSystem, $sourceType, $returnType);
        $this->name = $name;
        $this->isStatic = $isStatic;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isStatic()
    {
        return $this->isStatic;
    }
}
