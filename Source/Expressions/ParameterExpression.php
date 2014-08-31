<?php

namespace Pinq\Expressions;

/**
 * <code>
 * function (\stdClass &$I = null) {}
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterExpression extends Expression
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $typeHint;

    /**
     * @var Expression
     */
    private $defaultValue;

    /**
     * @var boolean
     */
    private $isPassedByReference;

    public function __construct(
            $name,
            $typeHint = null,
            Expression $defaultValue = null,
            $isPassedByReference = false
    ) {
        $this->name                = $name;
        $this->typeHint            = $typeHint;
        $this->defaultValue        = $defaultValue;
        $this->isPassedByReference = $isPassedByReference;
    }

    public function asEvaluator(IEvaluationContext $context = null)
    {
        throw static::cannotEvaluate();
    }

    public function simplify(IEvaluationContext $context = null)
    {
        return $this->update(
                $this->name,
                $this->typeHint,
                $this->defaultValue === null ? null : $this->defaultValue->simplify($context),
                $this->isPassedByReference
        );
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkParameter($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function hasTypeHint()
    {
        return $this->typeHint !== null;
    }

    /**
     * @return string|null
     */
    public function getTypeHint()
    {
        return $this->typeHint;
    }

    /**
     * @return boolean
     */
    public function hasDefaultValue()
    {
        return $this->defaultValue !== null;
    }

    /**
     * @return Expression|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return boolean
     */
    public function isPassedByReference()
    {
        return $this->isPassedByReference;
    }

    /**
     * @param string          $name
     * @param string|null     $typeHint
     * @param Expression|null $defaultValue
     * @param boolean         $isPassedByReference
     *
     * @return self
     */
    public function update($name, $typeHint, Expression $defaultValue = null, $isPassedByReference)
    {
        if ($this->name === $name
                && $this->typeHint === $typeHint
                && $this->defaultValue === $defaultValue
                && $this->isPassedByReference === $isPassedByReference
        ) {
            return $this;
        }

        return new self(
                $name,
                $typeHint,
                $defaultValue,
                $isPassedByReference);
    }

    protected function compileCode(&$code)
    {
        if ($this->typeHint !== null) {
            $code .= $this->typeHint . ' ';
        }

        if ($this->isPassedByReference) {
            $code .= '&';
        }

        $code .= '$' . $this->name;

        if ($this->defaultValue !== null) {
            $code .= ' = ';
            $this->defaultValue->compileCode($code);
        }
    }

    public function serialize()
    {
        return serialize(
                [
                        $this->defaultValue,
                        $this->isPassedByReference,
                        $this->name,
                        $this->typeHint
                ]
        );
    }

    public function unserialize($serialized)
    {
        list(
                $this->defaultValue,
                $this->isPassedByReference,
                $this->name,
                $this->typeHint) = unserialize($serialized);
    }

    public function __clone()
    {
        $this->defaultValue = $this->defaultValue === null
                ? null : clone $this->defaultValue;
    }
}
