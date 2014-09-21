<?php

namespace Pinq\Analysis\Types;

use Pinq\Analysis\ICompositeType;
use Pinq\Analysis\IType;
use Pinq\Expressions as O;

/**
 * Implementation of the composite type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CompositeType extends Type implements ICompositeType
{
    /**
     * @var IType[]
     */
    protected $composedTypes;

    public function __construct(
            $identifier,
            IType $parentType,
            array $composedTypes
    ) {
        parent::__construct($identifier, $parentType);
        $this->composedTypes = $composedTypes;
    }

    public function isParentTypeOf(IType $type)
    {
        foreach ($this->composedTypes as $composedType) {
            if ($composedType->isParentTypeOf($type)) {
                return true;
            }
        }

        return false;
    }

    public function getComposedTypes()
    {
        return $this->composedTypes;
    }

    protected function getTypeData($function, O\Expression $expression)
    {
        foreach ($this->composedTypes as $composedType) {
            try {
                return $composedType->$function($expression);
            } catch (\Exception $exception) {

            }
        }

        return parent::$function($expression);
    }

    public function getConstructor(O\NewExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getMethod(O\MethodCallExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getStaticMethod(O\StaticMethodCallExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getField(O\FieldExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getStaticField(O\StaticFieldExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getInvocation(O\InvocationExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getIndex(O\IndexExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getCast(O\CastExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }

    public function getUnaryOperation(O\UnaryOperationExpression $expression)
    {
        return $this->getTypeData(__FUNCTION__, $expression);
    }
}
