<?php

namespace Pinq\Analysis;

/**
 * Interface of a type system.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ITypeSystem
{
    /**
     * Gets of the type with the supplied identifier.
     *
     * @param string $typeIdentifier
     *
     * @return IType
     */
    public function getType($typeIdentifier);

    /**
     * Gets of the type of the supplied value.
     *
     * @param mixed $value
     *
     * @return IType
     * @throws TypeException If the type is not supported.
     */
    public function getTypeFromValue($value);

    /**
     * Gets of the type from the supplied parameter type hint.
     *
     * @param string|null $typeHint
     *
     * @return IType
     */
    public function getTypeFromTypeHint($typeHint);

    /**
     * Gets of a common ancestor type of the supplied types.
     *
     * @param IType $type
     * @param IType $otherType
     *
     * @return IType
     */
    public function getCommonAncestorType(IType $type, IType $otherType);

    /**
     * Gets the native type with the supplied int from the INativeType::TYPE_* constants.
     *
     * @param string $nativeType
     *
     * @return INativeType
     * @throws TypeException If the native type is not supported.
     */
    public function getNativeType($nativeType);

    /**
     * Gets the object type with the supplied class name.
     *
     * @param string $classType
     *
     * @return IObjectType
     * @throws TypeException If the class is not supported.
     */
    public function getObjectType($classType);

    /**
     * Gets a type composed of the supplied types.
     *
     * @param IType[] $types
     *
     * @return IType
     * @throws TypeException If the type is not supported.
     */
    public function getCompositeType(array $types);

    /**
     * Gets the function with the supplied name.
     *
     * @param string $name
     *
     * @return IFunction
     * @throws TypeException If the function is not supported.
     */
    public function getFunction($name);

    /**
     * Gets the binary operation matching the supplied types.
     *
     * @param IType  $leftOperandType
     * @param string $operator
     * @param IType  $rightOperandType
     *
     * @return IBinaryOperation
     * @throws TypeException    If the binary operation is not supported
     */
    public function getBinaryOperation(IType $leftOperandType, $operator, IType $rightOperandType);
}
