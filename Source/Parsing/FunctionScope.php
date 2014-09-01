<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Implementation of the function scope interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionScope implements IFunctionScope
{
    /**
     * @var object|null
     */
    protected $thisObject;

    /**
     * @var string|null
     */
    protected $thisType;

    /**
     * @var string|null
     */
    protected $scopeType;

    /**
     * @var array<string, mixed>
     */
    protected $variableValueMap;

    public function __construct(
            $thisObject,
            $scopeType,
            array $variableValueMap
    ) {
        $this->thisObject = $thisObject;
        $this->thisType = $thisObject !== null ? get_class($thisObject) : null;
        $this->scopeType = $scopeType;
        $this->variableValueMap = $variableValueMap;
    }

    /**
     * Creates a function scope instance from the supplied reflection and callable.
     *
     * @param \ReflectionFunctionAbstract $reflection
     * @param callable                    $callable
     *
     * @return self
     */
    public static function fromReflection(\ReflectionFunctionAbstract $reflection, callable $callable)
    {
        if (is_array($callable)) {
            /** @var $reflection \ReflectionMethod */
            $thisObject = is_object($callable[0]) ? $callable[0] : null;
            $scopeType = $reflection->getDeclaringClass()->getName();
        } elseif (is_object($callable) && !($callable instanceof \Closure)) {
            /** @var $reflection \ReflectionMethod */
            $thisObject = $callable;
            $scopeType = $reflection->getDeclaringClass()->getName();
        } elseif ($reflection->isClosure()) {
            $thisObject = $reflection->getClosureThis();
            $scopeClass = $reflection->getClosureScopeClass();
            $scopeType = $scopeClass === null ? null : $scopeClass->getName();
        } else {
            $thisObject = null;
            $scopeType = null;
        }
        $variableTable = $reflection->getStaticVariables();

        return new self($thisObject, $scopeType, $variableTable);
    }

    public function hasThis()
    {
        return $this->thisObject !== null;
    }

    public function getThis()
    {
        return $this->thisObject;
    }

    public function getThisType()
    {
        return $this->thisType;
    }

    public function getScopeType()
    {
        return $this->scopeType;
    }

    public function getVariableTable()
    {
        return $this->variableValueMap;
    }

    public function asEvaluationContext(array $variableTable = [], $namespace = null)
    {
        return new O\EvaluationContext(
                $namespace,
                $this->scopeType,
                $this->thisObject,
                //Used variables take precedence over arguments: http://3v4l.org/V4LSE
                $this->variableValueMap + $variableTable
        );
    }
}
