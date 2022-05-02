<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Implementation of the function signature interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionSignature extends MagicResolvable implements IFunctionSignature
{
    /**
     * @var int
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $returnsReference;

    /**
     * @var int|null
     */
    protected $accessModifier;

    /**
     * @var int|null
     */
    protected $polymorphModifier;

    /**
     * @var bool|null
     */
    protected $isStatic;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var O\ParameterExpression[]
     */
    protected $parameterExpressions;

    /**
     * @var string[]|null
     */
    protected $scopedVariableNames;

    /**
     * @var string
     */
    protected $hash;

    protected function __construct(
            $type,
            $returnsReference,
            $accessModifier,
            $polymorphModifier,
            $isStatic,
            $name,
            array $parameterExpressions,
            array $scopedVariableNames = null
    ) {
        parent::__construct($parameterExpressions);

        $this->type                 = $type;
        $this->returnsReference     = $returnsReference;
        $this->accessModifier       = $accessModifier;
        $this->polymorphModifier    = $polymorphModifier;
        $this->isStatic             = $isStatic;
        $this->parameterExpressions = $parameterExpressions;
        $this->name                 = $name;
        $this->scopedVariableNames  = $scopedVariableNames;

        $this->hash = implode(
                '-',
                [
                        $type,
                        $returnsReference,
                        $accessModifier,
                        $polymorphModifier,
                        $isStatic,
                        $name,
                        serialize($parameterExpressions),
                        $scopedVariableNames !== null ? implode('|', $scopedVariableNames) : '',
                ]
        );
    }

    protected function withResolvedMagic(array $resolvedExpressions)
    {
        return new self(
                $this->type,
                $this->returnsReference,
                $this->accessModifier,
                $this->polymorphModifier,
                $this->isStatic,
                $this->name,
                O\Expression::simplifyAll($resolvedExpressions),
                $this->scopedVariableNames);
    }

    /**
     * Creates a function signature instance from the supplied reflection.
     *
     * @param \ReflectionFunctionAbstract $reflection
     *
     * @return self
     */
    public static function fromReflection(\ReflectionFunctionAbstract $reflection)
    {
        $returnsReference     = $reflection->returnsReference();
        $name                 = $reflection->getShortName();
        $parameterExpressions = self::getParameterExpressionsFromReflection($reflection);

        if ($reflection->isClosure()) {
            return self::closure(
                    $returnsReference,
                    $parameterExpressions,
                    array_keys($reflection->getStaticVariables())
            );
        } elseif ($reflection instanceof \ReflectionMethod) {
            if ($reflection->isPublic()) {
                $accessModifier = self::ACCESS_PUBLIC;
            } elseif ($reflection->isProtected()) {
                $accessModifier = self::ACCESS_PROTECTED;
            } else {
                $accessModifier = self::ACCESS_PRIVATE;
            }

            if ($reflection->isAbstract()) {
                $polymorphModifier = self::POLYMORPH_ABSTRACT;
            } elseif ($reflection->isFinal()) {
                $polymorphModifier = self::POLYMORPH_FINAL;
            } else {
                $polymorphModifier = null;
            }

            return self::method(
                    $returnsReference,
                    $accessModifier,
                    $polymorphModifier,
                    $reflection->isStatic(),
                    $name,
                    $parameterExpressions
            );
        } else {
            return self::func(
                    $returnsReference,
                    $name,
                    $parameterExpressions
            );
        }
    }

    /**
     * Creates a function signature with the supplied parameters.
     *
     * @param boolean                 $returnsReference
     * @param string                  $name
     * @param O\ParameterExpression[] $parameterExpressions
     *
     * @return self
     */
    public static function func(
            $returnsReference,
            $name,
            array $parameterExpressions
    ) {
        return new self(
                self::TYPE_FUNCTION,
                $returnsReference,
                null,
                null,
                null,
                $name,
                $parameterExpressions,
                null);
    }

    /**
     * Creates a closure signature with the supplied parameters.
     *
     * @param boolean                 $returnsReference
     * @param O\ParameterExpression[] $parameterExpressions
     * @param string[]                $scopedVariableNames
     *
     * @return self
     */
    public static function closure(
            $returnsReference,
            array $parameterExpressions,
            array $scopedVariableNames
    ) {
        return new self(
                self::TYPE_CLOSURE,
                $returnsReference,
                null,
                null,
                null,
                null,
                $parameterExpressions,
                $scopedVariableNames);
    }

    /**
     * Creates a method signature with the supplied parameters.
     *
     * @param boolean                 $returnsReference
     * @param int|null                $accessModifier
     * @param int|null                $polymorphModifier
     * @param boolean                 $isStatic
     * @param string                  $name
     * @param O\ParameterExpression[] $parameterExpressions
     *
     * @return self
     */
    public static function method(
            $returnsReference,
            $accessModifier,
            $polymorphModifier,
            $isStatic,
            $name,
            array $parameterExpressions
    ) {
        return new self(
                self::TYPE_METHOD,
                $returnsReference,
                $accessModifier,
                $polymorphModifier,
                $isStatic,
                $name,
                $parameterExpressions,
                null);
    }

    protected static function getParameterExpressionsFromReflection(\ReflectionFunctionAbstract $reflection)
    {
        $parameterExpressions = [];

        foreach ($reflection->getParameters() as $parameter) {
            $parameterExpressions[] = self::getParameterExpression($parameter);
        }

        return $parameterExpressions;
    }

    private static function getParameterExpression(\ReflectionParameter $parameter)
    {
        $typeHint = null;
        $type = $parameter->getType();

        if ($type instanceof \ReflectionNamedType) {
            $typeHint = $type->getName();

            if ($typeHint === 'parent') {
                $typeHint = '\\' . $parameter->getDeclaringClass()->getParentClass()->getName();
            } else if ($typeHint === 'self') {
                $typeHint = '\\' . $parameter->getDeclaringClass()->getName();
            } elseif (! in_array($typeHint, ['int', 'float', 'string', 'bool', 'array', 'callable', 'iterable', 'object', 'void'])) {
                $typeHint = '\\' . $typeHint;
            }
        }

        return O\Expression::parameter(
                $parameter->getName(),
                $typeHint,
                $parameter->isDefaultValueAvailable() ? O\Expression::value($parameter->getDefaultValue()) : null,
                $parameter->isPassedByReference(),
                method_exists($parameter, 'isVariadic') && $parameter->isVariadic()
        );
    }

    public function getType()
    {
        return $this->type;
    }

    public function returnsReference()
    {
        return $this->returnsReference;
    }

    public function getAccessModifier()
    {
        return $this->accessModifier;
    }

    public function getPolymorphModifier()
    {
        return $this->polymorphModifier;
    }

    public function isStatic()
    {
        return $this->isStatic;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParameterExpressions()
    {
        return $this->parameterExpressions;
    }

    public function getScopedVariableNames()
    {
        return $this->scopedVariableNames;
    }

    public function getHash()
    {
        return $this->hash;
    }
}
