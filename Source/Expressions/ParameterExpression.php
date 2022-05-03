<?php

namespace Pinq\Expressions;

/**
 * <code>
 * function (\stdClass &$I = null) {}
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterExpression extends NamedParameterExpression
{
    /**
     * @var string|null
     */
    private $typeHint;

    /**
     * @var boolean
     */
    private $isVariadic;

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
            $isPassedByReference = false,
            $isVariadic = false
    ) {
        parent::__construct($name);
        $this->typeHint            = $typeHint;
        $this->defaultValue        = $defaultValue;
        $this->isPassedByReference = $isPassedByReference;
        $this->isVariadic          = $isVariadic;
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
                $this->isPassedByReference,
                $this->isVariadic
        );
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkParameter($this);
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

    public function isVariadic()
    {
        return $this->isVariadic;
    }

    /**
     * @param string          $name
     * @param string|null     $typeHint
     * @param Expression|null $defaultValue
     * @param boolean         $isPassedByReference
     * @param boolean         $isVariadic
     *
     * @return self
     */
    public function update($name, $typeHint, Expression $defaultValue = null, $isPassedByReference, $isVariadic)
    {
        if ($this->name === $name
                && $this->typeHint === $typeHint
                && $this->defaultValue === $defaultValue
                && $this->isPassedByReference === $isPassedByReference
                && $this->isVariadic === $isVariadic
        ) {
            return $this;
        }

        return new self(
                $name,
                $typeHint,
                $defaultValue,
                $isPassedByReference,
                $isVariadic);
    }

    protected function compileCode(&$code)
    {
        if ($this->typeHint !== null) {
            $code .= $this->typeHint . ' ';
        }

        if ($this->isPassedByReference) {
            $code .= '&';
        }

        if ($this->isVariadic) {
            $code .= '...';
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
                        $this->typeHint,
                        $this->isVariadic
                ]
        );
    }

    public function __serialize(): array
    {
        return [
                $this->defaultValue,
                $this->isPassedByReference,
                $this->name,
                $this->typeHint,
                $this->isVariadic
        ];
    }

    public function unserialize($serialized)
    {
        list(
                $this->defaultValue,
                $this->isPassedByReference,
                $this->name,
                $this->typeHint,
                $this->isVariadic) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list(
            $this->defaultValue,
            $this->isPassedByReference,
            $this->name,
            $this->typeHint,
            $this->isVariadic) = $data;
    }

    public function __clone()
    {
        $this->defaultValue = $this->defaultValue === null
                ? null : clone $this->defaultValue;
    }
}
