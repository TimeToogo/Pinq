<?php

namespace Pinq\Analysis\Types;

use Pinq\Analysis\IConstructor;
use Pinq\Analysis\IField;
use Pinq\Analysis\IMethod;
use Pinq\Analysis\IObjectType;
use Pinq\Analysis\IType;
use Pinq\Analysis\ITypeOperation;
use Pinq\Expressions as O;

/**
 * Base class of the object type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ObjectType extends Type implements IObjectType
{
    /**
     * @var string
     */
    protected $classType;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var IConstructor|null
     */
    protected $constructor;

    /**
     * @var IMethod[]
     */
    protected $methods = [];

    /**
     * @var IField[]
     */
    protected $fields = [];

    /**
     * @var ITypeOperation|IMethod|null
     */
    protected $invoker;

    /**
     * @param string                      $identifier
     * @param \ReflectionClass            $reflection
     * @param IType                       $parentType
     * @param IConstructor                $constructor
     * @param IMethod[]                   $methods
     * @param IField[]                    $fields
     * @param ITypeOperation[]            $unaryOperations
     * @param ITypeOperation[]            $castOperations
     * @param ITypeOperation|IMethod|null $invoker
     * @param ITypeOperation|null         $indexer
     */
    public function __construct(
            $identifier,
            \ReflectionClass $reflection,
            IType $parentType,
            IConstructor $constructor = null,
            array $methods = [],
            array $fields = [],
            array $unaryOperations = [],
            array $castOperations = [],
            ITypeOperation $invoker = null,
            ITypeOperation $indexer = null
    ) {
        parent::__construct($identifier, $parentType, $indexer, $unaryOperations, $castOperations);
        $this->classType   = $reflection->getName();
        $this->reflection  = $reflection;
        $this->invoker     = $invoker;
        $this->constructor = $constructor;
        $this->methods     = $methods;
        $this->fields      = $fields;
        $this->invoker     = $invoker;
    }

    public function getClassType()
    {
        return $this->reflection->getName();
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function isParentTypeOf(IType $type)
    {
        if ($type instanceof IObjectType) {
            return is_a($type->getClassType(), $this->classType, true);
        }

        return false;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getConstructor(O\NewExpression $expression)
    {
        if ($this->constructor !== null) {
            return $this->constructor;
        }

        return parent::getConstructor($expression);
    }

    public function getMethod(O\MethodCallExpression $expression)
    {
        if ($method = $this->getMethodByName($expression->getName(), false)) {
            return $method;
        }

        return parent::getMethod($expression);
    }

    public function getStaticMethod(O\StaticMethodCallExpression $expression)
    {
        if ($method = $this->getMethodByName($expression->getName(), true)) {
            return $method;
        }

        return parent::getStaticMethod($expression);
    }

    protected function getMethodByName(O\Expression $nameExpression, $mustBeStatic)
    {
        if ($nameExpression instanceof O\ValueExpression) {
            $methodName = $nameExpression->getValue();
            foreach ($this->methods as $otherMethodName => $method) {
                if ((!$mustBeStatic || $method->getReflection()->isStatic() === true)
                        && strcasecmp($methodName, $otherMethodName) === 0
                ) {
                    return $method;
                }
            }
        }

        return null;
    }

    public function getField(O\FieldExpression $expression)
    {
        if ($field = $this->getFieldByName($expression->getName(), false)) {
            return $field;
        }

        return parent::getField($expression);
    }

    public function getStaticField(O\StaticFieldExpression $expression)
    {
        if ($field = $this->getFieldByName($expression->getName(), true)) {
            return $field;
        }

        return parent::getStaticField($expression);
    }

    protected function getFieldByName(O\Expression $nameExpression, $static)
    {
        if ($nameExpression instanceof O\ValueExpression) {
            $fieldName = $nameExpression->getValue();

            foreach ($this->fields as $otherFieldName => $field) {
                if ($field->isStatic() === $static
                        && strcasecmp($fieldName, $otherFieldName) === 0
                ) {
                    return $field;
                }
            }
        }

        return null;
    }

    public function getInvocation(O\InvocationExpression $expression)
    {
        if ($this->invoker !== null) {
            return $this->invoker;
        }

        return parent::getInvocation($expression);
    }
}
