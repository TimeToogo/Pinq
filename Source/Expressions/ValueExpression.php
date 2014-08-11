<?php

namespace Pinq\Expressions;

use Pinq\PinqException;

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

    /**
     * @var \SplObjectStorage
     * @readOnly
     */
    private static $nonScalarValueExpressionAccess = [];

    public function __construct($value)
    {
        $this->value = $value;
        $this->initializeValue();
    }

    public static function __nonScalarValue__($hash)
    {
        return self::$nonScalarValueExpressionAccess[$hash];
    }

    protected function initializeValue()
    {
        self::$nonScalarValueExpressionAccess[spl_object_hash($this)] = $this->value;
    }

    public function __destruct()
    {
        unset(self::$nonScalarValueExpressionAccess[spl_object_hash($this)]);
    }

    public function simplifyToValue(IEvaluationContext $context = null)
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
        if (is_scalar($this->value) || $this->value === null || (is_array($this->value) && $this->isScalarArray($this->value))) {
            $code .= var_export($this->value, true);
        } else {
            $code .= '\\' . __CLASS__ . '::__nonScalarValue__(' . var_export(spl_object_hash($this), true) . ')';
        }
    }

    protected function isScalarArray(array $array)
    {
        $isScalar = true;
        array_walk_recursive($array,
                function ($value) use ($isScalar) {
                    if($isScalar && !(is_scalar($value) || $value === null)) {
                        $isScalar = false;
                    }
                });

        return $isScalar;
    }

    public function serialize()
    {
        return serialize([$this->value]);
    }

    public function unserialize($serialized)
    {
        list($this->value) = unserialize($serialized);
        $this->initializeValue();
    }

    public function __clone()
    {
        $this->value = is_object($this->value) ? clone $this->value : $this->value;
    }
}
