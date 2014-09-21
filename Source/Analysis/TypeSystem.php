<?php

namespace Pinq\Analysis;

use Pinq\Analysis\BinaryOperations\BinaryOperation;
use Pinq\Analysis\TypeOperations\TypeOperation;
use Pinq\Expressions\Operators;

/**
 * Base class of the type system.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class TypeSystem implements ITypeSystem
{
    /**
     * @var INativeType[]
     */
    protected $nativeTypes = [];

    /**
     * @var IObjectType[]
     */
    protected $objectTypes = [];

    /**
     * @var ICompositeType[]
     */
    protected $compositeTypes = [];

    /**
     * @var IType[]
     */
    protected $customTypes = [];

    /**
     * @var IFunction[]
     */
    protected $functions = [];

    /**
     * @var IBinaryOperation[]
     */
    protected $binaryOperations = [];

    public function __construct()
    {
        foreach ($this->buildNativeTypes() as $nativeType) {
            $this->nativeTypes[$nativeType->getTypeOfType()] = $nativeType;
        }

        foreach ($this->buildBinaryOperations() as $binaryOperation) {
            $this->binaryOperations[] = $binaryOperation;
        }
    }

    /**
     * Performs all necessary normalization to the class name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalizeClassName($name)
    {
        return $name;
    }

    /**
     * Performs all necessary normalization the function name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalizeFunctionName($name)
    {
        return $name;
    }

    protected function buildTypeOperations($type, array $operatorTypeMap = [])
    {
        return array_map(
                function ($returnType) use ($type) {
                    return new TypeOperation($this, $type, $returnType);
                },
                $operatorTypeMap
        );
    }

    /**
     * @return INativeType[]
     */
    abstract protected function nativeTypes();

    /**
     * @return INativeType[]
     */
    protected function buildNativeTypes()
    {
        return $this->nativeTypes();
    }

    /**
     * @return array[]
     */
    abstract protected function binaryOperations();

    /**
     * @return IBinaryOperation[]
     */
    protected function buildBinaryOperations()
    {
        $binaryOperations = [];
        foreach ($this->binaryOperations() as $operator) {
            $binaryOperations[] = new BinaryOperation($this, $operator[0], $operator[1], $operator[2], $operator['return']);
        }

        return $binaryOperations;
    }

    public function getType($typeIdentifier)
    {
        if (TypeId::isObject($typeIdentifier)) {
            return $this->getObjectType(TypeId::getClassTypeFromId($typeIdentifier));
        } elseif (TypeId::isComposite($typeIdentifier)) {
            return $this->getCompositeType(
                    array_map([$this, __FUNCTION__], TypeId::getComposedTypeIdsFromId($typeIdentifier))
            );
        } else {
            return $this->getNativeType($typeIdentifier);
        }
    }

    public function getNativeType($nativeType)
    {
        if (!isset($this->nativeTypes[$nativeType])) {
            throw new TypeException('Cannot get native type \'%s\': type is not supported', $nativeType);
        }

        return $this->nativeTypes[$nativeType];
    }

    /**
     * @param string $typeId
     * @param string $classType
     *
     * @return IObjectType
     */
    abstract protected function buildObjectType($typeId, $classType);

    public function getObjectType($classType)
    {
        $normalizedClassType = $this->normalizeClassName($classType);
        $typeId = TypeId::getObject($normalizedClassType);
        if (!isset($this->objectTypes[$typeId])) {
            $this->objectTypes[$typeId] = $this->buildObjectType($typeId, $normalizedClassType);
        }

        return $this->objectTypes[$typeId];
    }

    /**
     * @param string  $typeId
     * @param IType[] $types
     *
     * @return ICompositeType
     */
    abstract protected function buildCompositeType($typeId, array $types);

    public function getCompositeType(array $types)
    {
        $types = $this->flattenComposedTypes($types);

        //Remove any redundant types: (\Iterator and \Traversable) becomes \Iterator
        /** @var $types IType[] */
        foreach ($types as $outer => $outerType) {
            foreach ($types as $inner => $innerType) {
                if ($outer !== $inner && $innerType->isParentTypeOf($outerType)) {
                    unset($types[$inner]);
                }
            }
        }

        if (count($types) === 0) {
            return $this->getNativeType(INativeType::TYPE_MIXED);
        } elseif (count($types) === 1) {
            return reset($types);
        }

        ksort($types, SORT_STRING);
        $typeId = TypeId::getComposite(array_keys($types));
        if (!isset($this->compositeTypes[$typeId])) {
            $this->compositeTypes[$typeId] = $this->buildCompositeType($typeId, $types);
        }

        return $this->compositeTypes[$typeId];
    }

    /**
     * Flattens all the composed types.
     *
     * @param IType[] $types
     *
     * @return IType[]
     */
    protected function flattenComposedTypes(array $types)
    {
        $composedTypes = [];
        foreach ($types as $type) {
            if ($type instanceof ICompositeType) {
                $composedTypes += $this->flattenComposedTypes($type->getComposedTypes());
            } else {
                $composedTypes[$type->getIdentifier()] = $type;
            }
        }

        return $composedTypes;
    }

    /**
     * @param string $name
     *
     * @return IFunction
     */
    abstract protected function buildFunction($name);

    public function getFunction($name)
    {
        $normalizedName = $this->normalizeFunctionName($name);
        if (!isset($this->functions[$normalizedName])) {
            $this->functions[$normalizedName] = $this->buildFunction($normalizedName);
        }

        return $this->functions[$normalizedName];
    }

    public function getBinaryOperation(IType $leftOperandType, $operator, IType $rightOperandType)
    {
        foreach ($this->binaryOperations as $binaryOperation) {
            $leftOperand = $binaryOperation->getLeftOperandType();
            $rightOperand = $binaryOperation->getRightOperandType();

            if ($binaryOperation->getOperator() === $operator) {
                if (($leftOperand->isParentTypeOf($leftOperandType) && $rightOperand->isParentTypeOf($rightOperandType))
                        //Binary operators are symmetrical: test for flipped operands
                        || ($leftOperand->isParentTypeOf($rightOperandType)
                                && $rightOperand->isParentTypeOf($leftOperandType))
                ) {
                    return $binaryOperation;
                }
            }
        }

        throw new TypeException(
                'Cannot get binary operation: operation for \'%s\' %s \'%s\' is not supported',
                $leftOperandType->getIdentifier(),
                $operator,
                $rightOperandType->getIdentifier());
    }
}
