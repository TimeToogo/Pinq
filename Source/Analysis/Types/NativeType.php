<?php

namespace Pinq\Analysis\Types;

use Pinq\Analysis\INativeType;
use Pinq\Analysis\IType;
use Pinq\Analysis\ITypeOperation;

/**
 * Implementation of the native type. (eg arrays, strings, resources)
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NativeType extends Type implements INativeType
{
    /**
     * @var string
     */
    protected $typeOfType;

    public function __construct(
            $identifier,
            IType $parentType = null,
            $typeOfType,
            ITypeOperation $indexer = null,
            array $castOperations = [],
            array $unaryOperations = []
    ) {
        parent::__construct($identifier, $parentType);
        $this->typeOfType      = $typeOfType;
        $this->indexer         = $indexer;
        $this->castOperations  = $castOperations;
        $this->unaryOperations = $unaryOperations;
    }

    public function getTypeOfType()
    {
        return $this->typeOfType;
    }

    public function isParentTypeOf(IType $type)
    {
        return $this->isEqualTo($type);
    }
}
