<?php

namespace Pinq\Analysis\TypeData;

use Pinq\Analysis\INativeType;
use Pinq\Analysis\TypeId;

/**
 * Type data for date and time types / functions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DateTime extends TypeDataModule
{
    public function functions()
    {
        return [
                'time'       => INativeType::TYPE_INT,
                'mktime'     => INativeType::TYPE_INT,
                'gmmktime'   => INativeType::TYPE_INT,
                'strtotime'  => INativeType::TYPE_INT,
                'date'       => INativeType::TYPE_STRING,
                'idate'      => INativeType::TYPE_INT,
                'gmdate'     => INativeType::TYPE_STRING,
                'gmstrftime' => INativeType::TYPE_STRING,
                'strptime'   => INativeType::TYPE_ARRAY,
                'strftime'   => INativeType::TYPE_STRING,
                'localtime'  => INativeType::TYPE_ARRAY,
                'getdate'    => INativeType::TYPE_ARRAY,
                'checkdate'  => INativeType::TYPE_BOOL,
        ];
    }

    public function types()
    {
        return [
                'DateTime'     => [
                        'methods' => [
                                'add'              => self::TYPE_SELF,
                                'createFromFormat' => self::TYPE_SELF,
                                'getLastErrors'    => INativeType::TYPE_ARRAY,
                                'modify'           => self::TYPE_SELF,
                                'setDate'          => self::TYPE_SELF,
                                'setISODate'       => self::TYPE_SELF,
                                'setTime'          => self::TYPE_SELF,
                                'setTimestamp'     => self::TYPE_SELF,
                                'setTimezone'      => self::TYPE_SELF,
                                'sub'              => self::TYPE_SELF,
                                'diff'             => TypeId::getObject('DateInterval'),
                                'format'           => INativeType::TYPE_STRING,
                                'getOffset'        => INativeType::TYPE_INT,
                                'getTimestamp'     => INativeType::TYPE_INT,
                                'getTimezone'      => TypeId::getObject('DateTimeZone'),
                        ]
                ],
                'DateInterval' => [
                        'fields'  => [
                                'y'      => INativeType::TYPE_INT,
                                'm'      => INativeType::TYPE_INT,
                                'd'      => INativeType::TYPE_INT,
                                'h'      => INativeType::TYPE_INT,
                                'i'      => INativeType::TYPE_INT,
                                's'      => INativeType::TYPE_INT,
                                'invert' => INativeType::TYPE_INT,
                                'days'   => INativeType::TYPE_MIXED,
                        ],
                        'methods' => [
                                'createFromDateString' => self::TYPE_SELF,
                                'format'               => INativeType::TYPE_STRING,
                        ]
                ],
                'DateTimeZone' => [
                        'methods' => [
                                'getLocation'       => INativeType::TYPE_ARRAY,
                                'getName'           => INativeType::TYPE_STRING,
                                'getOffset'         => TypeId::getObject('DateTime'),
                                'getTransitions'    => INativeType::TYPE_ARRAY,
                                'listAbbreviations' => INativeType::TYPE_ARRAY,
                                'listIdentifiers'   => INativeType::TYPE_ARRAY,
                        ]
                ]
        ];
    }
}
