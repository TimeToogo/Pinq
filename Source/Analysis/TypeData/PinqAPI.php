<?php

namespace Pinq\Analysis\TypeData;

use Pinq;
use Pinq\Analysis\INativeType;
use Pinq\Analysis\TypeId;
use Pinq\Interfaces;
use Pinq\Iterators\IIteratorScheme;

/**
 * Type data for type the PINQ API.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PinqAPI extends TypeDataModule
{
    public function types()
    {
        $traversableInterfaceGroups = [
                Pinq\ITraversable::ITRAVERSABLE_TYPE => [
                        'ordered'    => Interfaces\IOrderedTraversable::IORDERED_TRAVERSABLE_TYPE,
                        'joining-on' => Interfaces\IJoiningOnTraversable::IJOINING_ON_TRAVERSABLE_TYPE,
                        'joining-to' => Interfaces\IJoiningToTraversable::IJOINING_TO_TRAVERSABLE_TYPE
                ],
                Pinq\ICollection::ICOLLECTION_TYPE   => [
                        'mutable'    => true,
                        'ordered'    => Interfaces\IOrderedCollection::IORDERED_COLLECTION_TYPE,
                        'joining-on' => Interfaces\IJoiningOnCollection::IJOINING_ON_COLLECTION_TYPE,
                        'joining-to' => Interfaces\IJoiningToCollection::IJOINING_TO_COLLECTION_TYPE
                ],
                Pinq\IQueryable::IQUERYABLE_TYPE     => [
                        'ordered'    => Interfaces\IOrderedQueryable::IORDERED_QUERYABLE_TYPE,
                        'joining-on' => Interfaces\IJoiningOnQueryable::IJOINING_ON_QUERYABLE_TYPE,
                        'joining-to' => Interfaces\IJoiningToQueryable::IJOINING_TO_QUERYABLE_TYPE
                ],
                Pinq\IRepository::IREPOSITORY_TYPE   => [
                        'mutable'    => true,
                        'ordered'    => Interfaces\IOrderedRepository::IORDERED_REPOSITORY_TYPE,
                        'joining-on' => Interfaces\IJoiningOnRepository::IJOINING_ON_REPOSITORY_TYPE,
                        'joining-to' => Interfaces\IJoiningToRepository::IJOINING_TO_REPOSITORY_TYPE
                ],
        ];

        $pinqTypes = [];
        foreach ($traversableInterfaceGroups as $traversableInterface => $traversableGroup) {
            $traversableType          = TypeId::getObject($traversableInterface);
            $orderedTraversableType   = TypeId::getObject($traversableGroup['ordered']);
            $joiningOnTraversableType = TypeId::getObject($traversableGroup['joining-on']);
            $joiningToTraversableType = TypeId::getObject($traversableGroup['joining-to']);

            $commonMethods = [
                    'asArray'           => INativeType::TYPE_ARRAY,
                    'asTraversable'     => TypeId::getObject(Pinq\ITraversable::ITRAVERSABLE_TYPE),
                    'asCollection'      => TypeId::getObject(Pinq\ICollection::ICOLLECTION_TYPE),
                    'isSource'          => INativeType::TYPE_BOOL,
                    'getSource'         => $traversableType,
                    'iterate'           => INativeType::TYPE_NULL,
                    'getIterator'       => TypeId::getObject('Traversable'),
                    'getTrueIterator'   => TypeId::getObject('Traversable'),
                    'getIteratorScheme' => TypeId::getObject(IIteratorScheme::IITERATOR_SCHEME_TYPE),
                    'first'             => INativeType::TYPE_MIXED,
                    'last'              => INativeType::TYPE_MIXED,
                    'count'             => INativeType::TYPE_INT,
                    'isEmpty'           => INativeType::TYPE_BOOL,
                    'aggregate'         => INativeType::TYPE_MIXED,
                    'maximum'           => INativeType::TYPE_MIXED,
                    'minimum'           => INativeType::TYPE_MIXED,
                    'sum'               => INativeType::TYPE_MIXED,
                    'average'           => INativeType::TYPE_MIXED,
                    'all'               => INativeType::TYPE_BOOL,
                    'any'               => INativeType::TYPE_BOOL,
                    'implode'           => INativeType::TYPE_STRING,
                    'contains'          => INativeType::TYPE_BOOL,
                    'where'             => $traversableType,
                    'orderBy'           => $orderedTraversableType,
                    'orderByAscending'  => $orderedTraversableType,
                    'orderByDescending' => $orderedTraversableType,
                    'skip'              => $traversableType,
                    'take'              => $traversableType,
                    'slice'             => $traversableType,
                    'indexBy'           => $traversableType,
                    'keys'              => $traversableType,
                    'reindex'           => $traversableType,
                    'groupBy'           => $traversableType,
                    'join'              => $joiningOnTraversableType,
                    'groupJoin'         => $joiningOnTraversableType,
                    'select'            => $traversableType,
                    'selectMany'        => $traversableType,
                    'unique'            => $traversableType,
                    'append'            => $traversableType,
                    'whereIn'           => $traversableType,
                    'except'            => $traversableType,
                    'union'             => $traversableType,
                    'intersect'         => $traversableType,
                    'difference'        => $traversableType,
            ];

            if (!empty($traversableGroup['mutable'])) {
                $commonMethods += [
                        'apply'       => INativeType::TYPE_NULL,
                        'addRange'    => INativeType::TYPE_NULL,
                        'remove'      => INativeType::TYPE_NULL,
                        'removeRange' => INativeType::TYPE_NULL,
                        'removeWhere' => INativeType::TYPE_NULL,
                        'clear'       => INativeType::TYPE_NULL,
                ];
            }

            $pinqTypes[$traversableInterface] = [
                    'methods' => $commonMethods
            ];

            $pinqTypes[$traversableGroup['ordered']] = [
                    'methods' => [
                                    'thenBy'           => $orderedTraversableType,
                                    'thenByAscending'  => $orderedTraversableType,
                                    'thenByDescending' => $orderedTraversableType,
                            ] + $commonMethods
            ];

            $joiningMethods = [
                    'withDefault' => $joiningToTraversableType,
                    'to'          => $traversableType,
            ];

            if (!empty($traversableGroup['mutable'])) {
                $joiningMethods += [
                        'apply' => INativeType::TYPE_NULL,
                ];
            }

            $pinqTypes[$traversableGroup['joining-to']] = [
                    'methods' => $joiningMethods
            ];

            $pinqTypes[$traversableGroup['joining-on']] = [
                    'methods' => [
                                    'on'         => $joiningToTraversableType,
                                    'onEquality' => $joiningToTraversableType,
                            ] + $joiningMethods
            ];
        }

        return $pinqTypes;
    }
} 