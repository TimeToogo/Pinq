<?php

namespace Pinq\Analysis\TypeData;

use Pinq\Analysis\INativeType;

/**
 * Type data for internal classes and interfaces.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InternalTypes extends TypeDataModule
{
    public function types()
    {
        return [
                'ArrayAccess' => [
                        'methods' => [
                                'offsetExists' => INativeType::TYPE_BOOL,
                                'offsetGet'    => INativeType::TYPE_MIXED,
                                'offsetSet'    => INativeType::TYPE_NULL,
                                'offsetUnset'  => INativeType::TYPE_NULL,
                        ]
                ],
                'Countable'   => [
                        'methods' => [
                                'count' => INativeType::TYPE_INT,
                        ]
                ],
                'Closure'     => [
                        'methods' => [
                                'bind'     => self::TYPE_SELF,
                                'bindTo'   => self::TYPE_SELF,
                                '__invoke' => INativeType::TYPE_MIXED
                        ]
                ],
                'Exception'   => [
                        'fields'  => [
                                'message' => INativeType::TYPE_STRING,
                                'code'    => INativeType::TYPE_INT,
                                'file'    => INativeType::TYPE_STRING,
                                'line'    => INativeType::TYPE_INT,
                        ],
                        'methods' => [
                                'getMessage'       => INativeType::TYPE_STRING,
                                'getFile'          => INativeType::TYPE_STRING,
                                'getLine'          => INativeType::TYPE_INT,
                                'getTrace'         => INativeType::TYPE_ARRAY,
                                'getTraceAsString' => INativeType::TYPE_STRING,
                        ]
                ],
        ];
    }
}
