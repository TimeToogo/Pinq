<?php

namespace Pinq\Analysis;

use Pinq;
use Pinq\Analysis\Functions\Func;
use Pinq\Analysis\TypeData\ITypeDataModule;
use Pinq\Analysis\TypeOperations\Constructor;
use Pinq\Analysis\TypeOperations\Field;
use Pinq\Analysis\TypeOperations\Indexer;
use Pinq\Analysis\TypeOperations\Method;
use Pinq\Analysis\Types\CompositeType;
use Pinq\Analysis\Types\MixedType;
use Pinq\Analysis\Types\NativeType;
use Pinq\Analysis\Types\ObjectType;
use Pinq\Expressions\Operators;

/**
 * Default implementation of the type system representing a subset
 * of PHP's excuse of a type system.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PhpTypeSystem extends TypeSystem
{
    const TYPE_SELF = '~~SELF_TYPE~~';

    /**
     * @var ITypeDataModule[]
     */
    protected $typeDataModules = [];

    /**
     * @var string[]
     */
    protected $functionTypeMap = [];

    /**
     * @var array[]
     */
    protected $classTypeMap = [];

    /**
     * @param ITypeDataModule[] $customTypeDataModules
     */
    public function __construct(array $customTypeDataModules = [])
    {
        parent::__construct();

        $typeDataModules = array_merge($this->typeDataModules(), $customTypeDataModules);
        /** @var $typeDataModules ITypeDataModule[] */
        foreach ($typeDataModules as $module) {
            $this->registerTypeDataModule($module);
        }
    }

    /**
     * @return ITypeDataModule[]
     */
    protected function typeDataModules()
    {
        return [
                new TypeData\InternalFunctions(),
                new TypeData\InternalTypes(),
                new TypeData\DateTime(),
                new TypeData\PinqAPI(),
        ];
    }

    /**
     * Gets the type data modules from the type system.
     *
     * @return ITypeDataModule[]
     */
    public function getTypeDataModules()
    {
        return $this->typeDataModules;
    }

    /**
     * Adds the type data module to the type system.
     *
     * @param ITypeDataModule $module
     *
     * @return void
     */
    public function registerTypeDataModule(ITypeDataModule $module)
    {
        $this->typeDataModules[] = $module;
        foreach ($module->functions() as $name => $returnType) {
            $normalizedFunctionName                         = $this->normalizeFunctionName($name);
            $this->functionTypeMap[$normalizedFunctionName] = $returnType;
            unset($this->functions[$normalizedFunctionName]);
        }

        foreach ($module->types() as $name => $typeData) {
            $normalizedClassName                      = $this->normalizeClassName($name);
            $this->classTypeMap[$normalizedClassName] = $typeData;
            unset($this->objectTypes[$normalizedClassName]);
        }
    }

    // Normalize function / type names using reflection to get originally defined name
    // but fallback to lower casing due to some functions that are not universally available
    // such as 'money_format' which will fail with reflection if not available.
    protected function normalizeClassName($name)
    {
        try {
            return (new \ReflectionClass($name))->getName();
        } catch (\Exception $exception) {
            return strtolower($name);
        }
    }

    protected function normalizeFunctionName($name)
    {
        try {
            return (new \ReflectionFunction($name))->getName();
        } catch (\Exception $exception) {
            return strtolower($name);
        }
    }

    protected function buildFunction($name)
    {
        return new Func(
                $this,
                $name,
                isset($this->functionTypeMap[$name]) ? $this->functionTypeMap[$name] : INativeType::TYPE_MIXED);
    }

    protected function buildCompositeType($typeId, array $types)
    {
        return new CompositeType(
                $typeId,
                $this->nativeTypes[INativeType::TYPE_MIXED],
                $types);
    }

    public function getCommonAncestorType(IType $type, IType $otherType)
    {
        if ($type->isEqualTo($otherType)) {
            return $type;
        } elseif ($type->isParentTypeOf($otherType)) {
            return $type;
        } elseif ($otherType->isParentTypeOf($type)) {
            return $otherType;
        }

        $parentTypes      = $this->getAncestorTypes($type);
        $otherParentTypes = $this->getAncestorTypes($otherType);

        /** @var $commonParentTypes IType[] */
        $commonParentTypes = array_intersect_key($parentTypes, $otherParentTypes);

        return $this->getCompositeType($commonParentTypes);
    }

    public function getTypeFromValue($value)
    {
        return $this->getType(TypeId::fromValue($value));
    }

    public function getTypeFromTypeHint($typeHint)
    {
        if ($typeHint === null || $typeHint === '') {
            return $this->nativeTypes[INativeType::TYPE_MIXED];
        }

        if (strcasecmp($typeHint, 'callable') === 0) {
            return $this->nativeTypes[INativeType::TYPE_MIXED];
        } elseif (strcasecmp($typeHint, 'array') === 0) {
            return $this->nativeTypes[INativeType::TYPE_ARRAY];
        } else {
            return $this->getObjectType($typeHint);
        }
    }

    protected function nativeCasts()
    {
        return [
                Operators\Cast::STRING     => INativeType::TYPE_STRING,
                Operators\Cast::BOOLEAN    => INativeType::TYPE_BOOL,
                Operators\Cast::INTEGER    => INativeType::TYPE_INT,
                Operators\Cast::DOUBLE     => INativeType::TYPE_DOUBLE,
                Operators\Cast::ARRAY_CAST => INativeType::TYPE_ARRAY,
                Operators\Cast::OBJECT     => TypeId::getObject('stdClass'),
        ];
    }

    protected function nativeType(
            $typeOfType,
            IType $parentType,
            IIndexer $indexer = null,
            array $unaryOperatorMap = [],
            array $castMap = []
    ) {
        return new NativeType(
                $typeOfType,
                $parentType,
                $typeOfType,
                $indexer,
                $this->buildTypeOperations($typeOfType, array_filter($castMap + $this->nativeCasts())),
                $this->buildTypeOperations(
                        $typeOfType,
                        array_filter($unaryOperatorMap + $this->commonNativeUnaryOperations())
                ));
    }

    protected function commonNativeUnaryOperations()
    {
        return [
                Operators\Unary::NOT      => INativeType::TYPE_BOOL,
                Operators\Unary::PLUS     => INativeType::TYPE_INT,
                Operators\Unary::NEGATION => INativeType::TYPE_INT,
        ];
    }

    protected function nativeTypes()
    {
        return [
                $mixedType = new MixedType(INativeType::TYPE_MIXED),
                $numericType = $this->nativeType(
                        INativeType::TYPE_NUMERIC,
                        $mixedType,
                        null,
                        [
                                Operators\Unary::BITWISE_NOT   => INativeType::TYPE_INT,
                                Operators\Unary::PLUS          => INativeType::TYPE_NUMERIC,
                                Operators\Unary::NEGATION      => INativeType::TYPE_NUMERIC,
                                Operators\Unary::INCREMENT     => INativeType::TYPE_NUMERIC,
                                Operators\Unary::DECREMENT     => INativeType::TYPE_NUMERIC,
                                Operators\Unary::PRE_INCREMENT => INativeType::TYPE_NUMERIC,
                                Operators\Unary::PRE_DECREMENT => INativeType::TYPE_NUMERIC,
                        ]
                ),
                $this->nativeType(
                        INativeType::TYPE_STRING,
                        $mixedType,
                        new Indexer($this, INativeType::TYPE_STRING, INativeType::TYPE_STRING),
                        [
                                Operators\Unary::BITWISE_NOT   => INativeType::TYPE_STRING,
                                Operators\Unary::INCREMENT     => INativeType::TYPE_STRING,
                                Operators\Unary::DECREMENT     => INativeType::TYPE_STRING,
                                Operators\Unary::PRE_INCREMENT => INativeType::TYPE_MIXED,
                                Operators\Unary::PRE_DECREMENT => INativeType::TYPE_MIXED,
                        ]
                ),
                $this->nativeType(
                        INativeType::TYPE_ARRAY,
                        $mixedType,
                        new Indexer($this, INativeType::TYPE_ARRAY, INativeType::TYPE_MIXED),
                        [
                                Operators\Unary::PLUS     => null,
                                Operators\Unary::NEGATION => null,
                        ],
                        [
                                Operators\Cast::STRING => null,
                        ]
                ),
                $this->nativeType(
                        INativeType::TYPE_INT,
                        $numericType,
                        null,
                        [
                                Operators\Unary::BITWISE_NOT   => INativeType::TYPE_INT,
                                Operators\Unary::INCREMENT     => INativeType::TYPE_INT,
                                Operators\Unary::DECREMENT     => INativeType::TYPE_INT,
                                Operators\Unary::PRE_INCREMENT => INativeType::TYPE_INT,
                                Operators\Unary::PRE_DECREMENT => INativeType::TYPE_INT,
                        ]
                ),
                $this->nativeType(
                        INativeType::TYPE_DOUBLE,
                        $numericType,
                        null,
                        [
                                Operators\Unary::BITWISE_NOT   => INativeType::TYPE_INT,
                                Operators\Unary::PLUS          => INativeType::TYPE_DOUBLE,
                                Operators\Unary::NEGATION      => INativeType::TYPE_DOUBLE,
                                Operators\Unary::INCREMENT     => INativeType::TYPE_DOUBLE,
                                Operators\Unary::DECREMENT     => INativeType::TYPE_DOUBLE,
                                Operators\Unary::PRE_INCREMENT => INativeType::TYPE_DOUBLE,
                                Operators\Unary::PRE_DECREMENT => INativeType::TYPE_DOUBLE,
                        ]
                ),
                $this->nativeType(
                        INativeType::TYPE_BOOL,
                        $mixedType,
                        null,
                        [
                                Operators\Unary::INCREMENT     => INativeType::TYPE_BOOL,
                                Operators\Unary::DECREMENT     => INativeType::TYPE_BOOL,
                                Operators\Unary::PRE_INCREMENT => INativeType::TYPE_BOOL,
                                Operators\Unary::PRE_DECREMENT => INativeType::TYPE_BOOL,
                        ]
                ),
                $this->nativeType(INativeType::TYPE_NULL, $mixedType),
                $this->nativeType(INativeType::TYPE_RESOURCE, $mixedType),
        ];
    }

    protected function getAncestorTypes(IType $type)
    {
        $ancestorTypes = [$type->getIdentifier() => $type];

        if (!$type->hasParentType()) {
            return $ancestorTypes;
        }

        if ($type instanceof ICompositeType) {
            foreach ($type->getComposedTypes() as $composedType) {
                $ancestorTypes += $this->getAncestorTypes($composedType);
            }
        } else {
            $parentType                                  = $type->getParentType();
            $ancestorTypes[$parentType->getIdentifier()] = $parentType;
            $ancestorTypes += $this->getAncestorTypes($parentType);
        }

        return $ancestorTypes;
    }

    protected function getObjectTypeData($classType)
    {
        $classType = $this->normalizeClassName($classType);
        $data      = isset($this->classTypeMap[$classType]) ? $this->classTypeMap[$classType] : [];

        foreach (['methods', 'fields', 'static-fields'] as $property) {
            if (!isset($data[$property])) {
                $data[$property] = [];
            }

            foreach ($data[$property] as &$returnType) {
                if ($returnType === self::TYPE_SELF) {
                    $returnType = TypeId::getObject($classType);
                }
            }
        }

        return $data;
    }

    protected function buildObjectType($typeId, $classType)
    {
        $typeData            = $this->getObjectTypeData($classType);
        $methodReturnTypeMap = $typeData['methods'];
        $fieldTypeMap        = $typeData['fields'];
        $staticFieldTypeMap  = $typeData['static-fields'];

        $reflection      = new \ReflectionClass($classType);
        $constructor     = new Constructor($this, $typeId, $reflection->getConstructor());
        $methods         = [];
        $fields          = [];
        $indexer         = null;
        $invoker         = null;
        $unaryOperations = $this->buildTypeOperations($this->objectUnaryOperations($typeId));
        $casts           = $this->buildTypeOperations($this->objectCasts($typeId));

        $parentTypes = array_map([$this, 'getObjectType'], $reflection->getInterfaceNames());
        if ($parentClass = $reflection->getParentClass()) {
            $parentTypes[] = $this->getObjectType($parentClass->getName());
        }

        $parentType      = $this->getCompositeType($parentTypes);

        if ($reflection->hasMethod('__toString')) {
            $methodReturnTypeMap += ['__toString' => INativeType::TYPE_STRING];
        }

        foreach ($methodReturnTypeMap as $name => $type) {
            $methods[$name] = new Method($this, $typeId, $reflection->getMethod($name), $type);
        }

        foreach ($reflection->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() === $classType
                    && !isset($methods[$method->getName()])
            ) {
                $methods[$method->getName()] = new Method($this, $typeId, $method, INativeType::TYPE_MIXED);
            }
        }

        foreach ($staticFieldTypeMap + $fieldTypeMap as $name => $type) {
            $fields[$name] = new Field($this, $typeId, $name, isset($staticFieldTypeMap[$name]), $type);
        }

        foreach ($reflection->getProperties() as $field) {
            if ($field->getDeclaringClass()->getName() === $classType
                    && !isset($fields[$field->getName()])
            ) {
                $fields[$field->getName()] = new Field($this, $typeId, $field->getName(), $field->isStatic(
                ), INativeType::TYPE_MIXED);
            }
        }

        if ($reflection->implementsInterface('ArrayAccess') && isset($methods['offsetGet'])) {
            $indexer = $methods['offsetGet'];
        }

        if (isset($methods['__invoke'])) {
            $invoker = $methods['__invoke'];
        }

        if (isset($methods['__toString'])) {
            $casts[Operators\Cast::STRING] = $methods['__toString'];
        }

        return new ObjectType(
                $typeId,
                $reflection,
                $parentType,
                $constructor,
                $methods,
                $fields,
                $unaryOperations,
                $casts,
                $invoker,
                $indexer);
    }

    protected function objectCasts($objectTypeId)
    {
        return [
                Operators\Cast::ARRAY_CAST => INativeType::TYPE_ARRAY,
                Operators\Cast::OBJECT     => $objectTypeId,
        ];
    }

    protected function objectUnaryOperations($objectTypeId)
    {
        return [
                Operators\Unary::NOT           => INativeType::TYPE_BOOL,
                Operators\Unary::INCREMENT     => $objectTypeId,
                Operators\Unary::DECREMENT     => $objectTypeId,
                Operators\Unary::PRE_INCREMENT => $objectTypeId,
                Operators\Unary::PRE_DECREMENT => $objectTypeId,
        ];
    }

    protected function booleanOperator($operator)
    {
        return [INativeType::TYPE_MIXED, $operator, INativeType::TYPE_MIXED, 'return' => INativeType::TYPE_BOOL];
    }

    protected function mathOperators($operator, $otherIntReturnType = INativeType::TYPE_INT)
    {
        //TODO: remove duplicate operators with types on opposite sides (binary operators are symmetrical)
        $operators = [];
        foreach ([
                         INativeType::TYPE_INT,
                         INativeType::TYPE_DOUBLE,
                         INativeType::TYPE_NUMERIC,
                         INativeType::TYPE_STRING,
                         INativeType::TYPE_RESOURCE,
                         INativeType::TYPE_BOOL,
                         INativeType::TYPE_NULL
                 ] as $type) {
            $operators = array_merge(
                    $operators,
                    [
                            [$type, $operator, INativeType::TYPE_NULL, 'return' => $otherIntReturnType],
                            [$type, $operator, INativeType::TYPE_BOOL, 'return' => $otherIntReturnType],
                            [$type, $operator, INativeType::TYPE_STRING, 'return' => INativeType::TYPE_NUMERIC],
                            [$type, $operator, INativeType::TYPE_RESOURCE, 'return' => $otherIntReturnType],
                    ]
            );
        }

        $operators[] = [INativeType::TYPE_INT, $operator, INativeType::TYPE_INT, 'return' => $otherIntReturnType];
        $operators[] = [
                INativeType::TYPE_INT,
                $operator,
                INativeType::TYPE_DOUBLE,
                'return' => INativeType::TYPE_DOUBLE
        ];
        $operators[] = [
                INativeType::TYPE_DOUBLE,
                $operator,
                INativeType::TYPE_DOUBLE,
                'return' => INativeType::TYPE_DOUBLE
        ];

        return $operators;
    }

    protected function bitwiseOperators($operator)
    {
        //TODO: remove duplicate operators with types on opposite sides (binary operators are symmetrical)
        $operators = [];
        foreach ([
                         INativeType::TYPE_INT,
                         INativeType::TYPE_DOUBLE,
                         INativeType::TYPE_NUMERIC,
                         INativeType::TYPE_STRING,
                         INativeType::TYPE_RESOURCE,
                         INativeType::TYPE_BOOL,
                         INativeType::TYPE_NULL
                 ] as $type) {
            $operators = array_merge(
                    $operators,
                    [
                            [$type, $operator, INativeType::TYPE_INT, 'return' => INativeType::TYPE_INT],
                            [$type, $operator, INativeType::TYPE_DOUBLE, 'return' => INativeType::TYPE_INT],
                            [$type, $operator, INativeType::TYPE_NULL, 'return' => INativeType::TYPE_INT],
                            [$type, $operator, INativeType::TYPE_BOOL, 'return' => INativeType::TYPE_INT],
                            [$type, $operator, INativeType::TYPE_STRING, 'return' => INativeType::TYPE_INT],
                            [$type, $operator, INativeType::TYPE_RESOURCE, 'return' => INativeType::TYPE_INT],
                    ]
            );
        }

        return $operators;
    }

    protected function binaryOperations()
    {
        return array_merge(
                [
                        $this->booleanOperator(Operators\Binary::EQUALITY),
                        $this->booleanOperator(Operators\Binary::INEQUALITY),
                        $this->booleanOperator(Operators\Binary::IDENTITY),
                        $this->booleanOperator(Operators\Binary::NOT_IDENTICAL),
                        $this->booleanOperator(Operators\Binary::GREATER_THAN),
                        $this->booleanOperator(Operators\Binary::GREATER_THAN_OR_EQUAL_TO),
                        $this->booleanOperator(Operators\Binary::LESS_THAN),
                        $this->booleanOperator(Operators\Binary::LESS_THAN_OR_EQUAL_TO),
                        $this->booleanOperator(Operators\Binary::IS_INSTANCE_OF),
                        $this->booleanOperator(Operators\Binary::EQUALITY),
                        $this->booleanOperator(Operators\Binary::LOGICAL_AND),
                        $this->booleanOperator(Operators\Binary::LOGICAL_OR),
                        [
                                INativeType::TYPE_MIXED,
                                Operators\Binary::CONCATENATION,
                                INativeType::TYPE_MIXED,
                                'return' => INativeType::TYPE_STRING
                        ],
                        [
                                INativeType::TYPE_ARRAY,
                                Operators\Binary::ADDITION,
                                INativeType::TYPE_ARRAY,
                                'return' => INativeType::TYPE_ARRAY
                        ],
                ],
                $this->mathOperators(Operators\Binary::ADDITION),
                $this->mathOperators(Operators\Binary::SUBTRACTION),
                $this->mathOperators(Operators\Binary::MULTIPLICATION),
                $this->mathOperators(Operators\Binary::DIVISION, INativeType::TYPE_NUMERIC),
                $this->mathOperators(Operators\Binary::MODULUS),
                $this->mathOperators(Operators\Binary::POWER),
                $this->bitwiseOperators(Operators\Binary::BITWISE_AND),
                $this->bitwiseOperators(Operators\Binary::BITWISE_OR),
                $this->bitwiseOperators(Operators\Binary::BITWISE_XOR),
                $this->bitwiseOperators(Operators\Binary::SHIFT_RIGHT),
                $this->bitwiseOperators(Operators\Binary::SHIFT_LEFT)
        );
    }
}
