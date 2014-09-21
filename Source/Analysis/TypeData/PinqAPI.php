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
            $pinqTypes += $this->generatePinqTypeData(
                    $traversableInterface,
                    $traversableGroup['ordered'],
                    $traversableGroup['joining-on'],
                    $traversableGroup['joining-to'],
                    !empty($traversableGroup['mutable'])
            );
        }

        return $pinqTypes;
    }

    /**
     * @param string $traversableType
     * @param string $orderedTraversableType
     * @param string $joiningOnTraversableType
     * @param string $joiningToTraversableType
     * @param bool   $mutable
     *
     * @return array
     */
    protected function generatePinqTypeData(
            $traversableType,
            $orderedTraversableType,
            $joiningOnTraversableType,
            $joiningToTraversableType,
            $mutable = false
    ) {
        $pinqTypes                  = [];
        $traversableTypeId          = TypeId::getObject($traversableType);
        $orderedTraversableTypeId   = TypeId::getObject($orderedTraversableType);
        $joiningOnTraversableTypeId = TypeId::getObject($joiningOnTraversableType);
        $joiningToTraversableTypeId = TypeId::getObject($joiningToTraversableType);

        $commonMethods = [
                'asArray'           => INativeType::TYPE_ARRAY,
                'asTraversable'     => TypeId::getObject(Pinq\ITraversable::ITRAVERSABLE_TYPE),
                'asCollection'      => TypeId::getObject(Pinq\ICollection::ICOLLECTION_TYPE),
                'isSource'          => INativeType::TYPE_BOOL,
                'getSource'         => $traversableTypeId,
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
                'where'             => $traversableTypeId,
                'orderBy'           => $orderedTraversableTypeId,
                'orderByAscending'  => $orderedTraversableTypeId,
                'orderByDescending' => $orderedTraversableTypeId,
                'skip'              => $traversableTypeId,
                'take'              => $traversableTypeId,
                'slice'             => $traversableTypeId,
                'indexBy'           => $traversableTypeId,
                'keys'              => $traversableTypeId,
                'reindex'           => $traversableTypeId,
                'groupBy'           => $traversableTypeId,
                'join'              => $joiningOnTraversableTypeId,
                'groupJoin'         => $joiningOnTraversableTypeId,
                'select'            => $traversableTypeId,
                'selectMany'        => $traversableTypeId,
                'unique'            => $traversableTypeId,
                'append'            => $traversableTypeId,
                'whereIn'           => $traversableTypeId,
                'except'            => $traversableTypeId,
                'union'             => $traversableTypeId,
                'intersect'         => $traversableTypeId,
                'difference'        => $traversableTypeId,
        ];

        if ($mutable) {
            $commonMethods += [
                    'apply'       => INativeType::TYPE_NULL,
                    'addRange'    => INativeType::TYPE_NULL,
                    'remove'      => INativeType::TYPE_NULL,
                    'removeRange' => INativeType::TYPE_NULL,
                    'removeWhere' => INativeType::TYPE_NULL,
                    'clear'       => INativeType::TYPE_NULL,
            ];
        }

        $pinqTypes[$traversableType] = [
                'methods' => $commonMethods
        ];

        $pinqTypes[$orderedTraversableType] = [
                'methods' => [
                                'thenBy'           => $orderedTraversableTypeId,
                                'thenByAscending'  => $orderedTraversableTypeId,
                                'thenByDescending' => $orderedTraversableTypeId,
                        ] + $commonMethods
        ];

        $joiningMethods = [
                'withDefault' => $joiningToTraversableTypeId,
                'to'          => $traversableTypeId,
        ];

        if ($mutable) {
            $joiningMethods += [
                    'apply' => INativeType::TYPE_NULL,
            ];
        }

        $pinqTypes[$joiningToTraversableType] = [
                'methods' => $joiningMethods
        ];

        $pinqTypes[$joiningOnTraversableType] = [
                'methods' => [
                                'on'         => $joiningToTraversableTypeId,
                                'onEquality' => $joiningToTraversableTypeId,
                        ] + $joiningMethods
        ];

        return $pinqTypes;
    }
}
