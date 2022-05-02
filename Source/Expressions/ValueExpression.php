<?php

namespace Pinq\Expressions;

use Pinq\PinqException;
use Pinq\Utilities;

/**
 * <code>
 * 1, 'foo', [], null etc
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ValueExpression extends Expression
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function asEvaluator(IEvaluationContext $context = null)
    {
        return new StaticValueEvaluator($this->value, $context);
    }

    public function evaluate(IEvaluationContext $context = null)
    {
        return $this->value;
    }

    public function simplify(IEvaluationContext $context = null)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkValue($this);
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function update($value)
    {
        if ($this->value === $value) {
            return $this;
        }

        return new self($value);
    }

    protected function compileCode(&$code)
    {
        if (self::isValueType($this->value)) {
            $code .= var_export($this->value, true);
        } else {
            throw new PinqException(
                    'Cannot compile %s to code: value must be a value type (scalar or array of scalars), currently %s',
                    get_class($this),
                    Utilities::getTypeOrClass($this->value));
        }
    }

    /**
     * Returns whether the supplied value is a value type.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValueType($value)
    {
        if (is_scalar($value) || $value === null) {
            return true;
        } elseif (is_array($value)) {
            $isScalar = true;
            array_walk_recursive($value,
                    function ($value) use (&$isScalar) {
                        if ($isScalar && !(is_scalar($value) || $value === null)) {
                            $isScalar = false;
                        }
                    });

            return $isScalar;
        } else {
            return false;
        }
    }

    public function serialize()
    {
        return serialize([$this->value]);
    }

    public function __serialize()
    {
        return [$this->value];
    }

    public function unserialize($serialized)
    {
        list($this->value) = unserialize($serialized);
    }

    public function __unserialize($data)
    {
        list($this->value) = $data;
    }

    public function __clone()
    {
        $this->value = is_object($this->value) ? clone $this->value : $this->value;
    }
}
