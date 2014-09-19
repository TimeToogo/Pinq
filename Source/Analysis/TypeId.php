<?php

namespace Pinq\Analysis;

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
} 