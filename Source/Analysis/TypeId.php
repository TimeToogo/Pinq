<?php

namespace Pinq\Analysis;

use Pinq\PinqException;

/**
 * Static helper to generate and interpret type identifiers.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class TypeId
{
    private function __construct()
    {

    }

    public static function getObject($class)
    {
        return 'object:' . $class;
    }

    public static function isObject($id)
    {
        return strpos($id, 'object:') === 0;
    }

    public static function getClassTypeFromId($objectId)
    {
        return substr($objectId, strlen('object:'));
    }

    public static function getComposite(array $typeIds)
    {
        return 'composite<' . implode('|', $typeIds) . '>';
    }

    public static function isComposite($id)
    {
        return strpos($id, 'composite<') === 0 && $id[strlen($id) - 1] === '>';
    }

    public static function getComposedTypeIdsFromId($compositeId)
    {
        return explode('|', substr($compositeId, strlen('composite<'), -strlen('>')));
    }

    public static function fromValue($value)
    {
        switch (gettype($value)) {
            case 'string':
                return INativeType::TYPE_STRING;

            case 'integer':
                return INativeType::TYPE_INT;

            case 'boolean':
                return INativeType::TYPE_BOOL;

            case 'double':
                return INativeType::TYPE_DOUBLE;

            case 'NULL':
                return INativeType::TYPE_NULL;

            case 'array':
                return INativeType::TYPE_ARRAY;

            case 'resource':
            case 'unknown type':
                return INativeType::TYPE_RESOURCE;

            case 'object':
                return self::getObject(get_class($value));
        }

        throw new PinqException('Unknown variable type %s given', gettype($value));
    }
}
