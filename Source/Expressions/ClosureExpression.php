<?php

namespace Pinq\Expressions;

/**
 * <code>
 * function ($I) { return $I + 5; }
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClosureExpression extends Expression
{
    /**
     * @var boolean
     */
    private $returnsReference;

    /**
     * @var boolean
     */
    private $isStatic;

    /**
     * @var ParameterExpression[]
     */
    private $parameters;

    /**
     * @var ClosureUsedVariableExpression[]
     */
    private $usedVariables;

    /**
     * @var string[]
     */
    private $usedVariableNames = [];

    /**
     * @var Expression[]
     */
    private $bodyExpressions;

    public function __construct(
            $returnsReference,
            $isStatic,
            array $parameterExpressions,
            array $usedVariables,
            array $bodyExpressions
    ) {
        $this->returnsReference = $returnsReference;
        $this->isStatic         = $isStatic;
        $this->parameters       = self::verifyAll($parameterExpressions, ParameterExpression::getType());
        $this->usedVariables    = self::verifyAll($usedVariables, ClosureUsedVariableExpression::getType());
        $this->bodyExpressions  = self::verifyAll($bodyExpressions);

        foreach ($this->usedVariables as $usedVariable) {
            $this->usedVariableNames[] = $usedVariable->getName();
        }
    }

    /**
     * @return boolean
     */
    public function returnsReference()
    {
        return $this->returnsReference;
    }

    /**
     * @return boolean
     */
    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * @return ParameterExpression[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string[]
     */
    public function getUsedVariableNames()
    {
        return $this->usedVariableNames;
    }

    /**
     * @return ClosureUsedVariableExpression[]
     */
    public function getUsedVariables()
    {
        return $this->usedVariables;
    }

    /**
     * @return Expression[]
     */
    public function getBodyExpressions()
    {
        return $this->bodyExpressions;
    }

    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkClosure($this);
    }

    /**
     * @param boolean                         $returnsReference
     * @param boolean                         $isStatic
     * @param ParameterExpression[]           $parameterExpressions
     * @param ClosureUsedVariableExpression[] $usedVariables
     * @param Expression[]                    $bodyExpressions
     *
     * @return self
     */
    public function update(
            $returnsReference,
            $isStatic,
            array $parameterExpressions,
            array $usedVariables,
            array $bodyExpressions
    ) {
        if ($this->returnsReference === $returnsReference
                && $this->isStatic === $isStatic
                && $this->parameters === $parameterExpressions
                && $this->usedVariables === $usedVariables
                && $this->bodyExpressions === $bodyExpressions
        ) {
            return $this;
        }

        return new self(
                $returnsReference,
                $isStatic,
                $parameterExpressions,
                $usedVariables,
                $bodyExpressions);
    }

    protected function compileCode(&$code)
    {
        if ($this->isStatic) {
            $code .= 'static ';
        }
        $code .= 'function ';

        if ($this->returnsReference) {
            $code .= '& ';
        }

        $code .= '(';
        if (!empty($this->parameters)) {
            $code .= implode(',', self::compileAll($this->parameters));
        }

        $code .= ')';

        if (!empty($this->usedVariables)) {
            $code .= 'use (';
            $code .= implode(',', self::compileAll($this->usedVariables));
            $code .= ')';
        }

        $code .= '{';

        foreach ($this->bodyExpressions as $expression) {
            $expression->compileCode($code);
            $code .= ';';
        }

        $code .= '}';
    }

    public function serialize()
    {
        return serialize(
                [
                        $this->returnsReference,
                        $this->isStatic,
                        $this->parameters,
                        $this->usedVariables,
                        $this->usedVariableNames,
                        $this->bodyExpressions
                ]
        );
    }

    public function __serialize(): array
    {
        return [
            $this->returnsReference,
            $this->isStatic,
            $this->parameters,
            $this->usedVariables,
            $this->usedVariableNames,
            $this->bodyExpressions
        ];
    }

    public function unserialize($serialized)
    {
        list(
                $this->returnsReference,
                $this->isStatic,
                $this->parameters,
                $this->usedVariables,
                $this->usedVariableNames,
                $this->bodyExpressions) = unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        list(
            $this->returnsReference,
            $this->isStatic,
            $this->parameters,
            $this->usedVariables,
            $this->usedVariableNames,
            $this->bodyExpressions) = $data;
    }
    
    public function __clone()
    {
        $this->parameters      = self::cloneAll($this->parameters);
        $this->bodyExpressions = self::cloneAll($this->bodyExpressions);
        $this->usedVariables   = self::cloneAll($this->usedVariables);
    }
}
