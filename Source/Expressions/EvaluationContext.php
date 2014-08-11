<?php

namespace Pinq\Expressions;

/**
 * Implementation of the evaluation context.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EvaluationContext implements IEvaluationContext
{
    /**
     * @var string|null
     */
    protected $scopeType;

    /**
     * @var string|null
     */
    protected $namespace;

    /**
     * @var object|null
     */
    protected $thisObject;

    /**
     * @var array<string, mixed>
     */
    protected $variableValueMap;

    public function __construct($namespace, $scopeType, $thisObject = null, array $variableValueMap = [])
    {
        $this->namespace        = $namespace;
        $this->scopeType        = $scopeType;
        $this->thisObject       = $thisObject;
        $this->variableValueMap = $variableValueMap;
    }

    /**
     * @param object|null $thisObject
     * @param array       $variableValueMap
     *
     * @return IEvaluationContext
     */
    public static function globalScope($thisObject = null, $variableValueMap = [])
    {
        return new self(null, null, $thisObject, $variableValueMap);
    }

    /**
     * @param string|null $namespace
     * @param string|null $scopeType
     *
     * @return IEvaluationContext
     */
    public static function staticContext($namespace, $scopeType)
    {
        return new self($namespace, $scopeType);
    }

    public function hasNamespace()
    {
        return $this->namespace !== null;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function hasScopeType()
    {
        return $this->scopeType !== null;
    }

    public function getScopeType()
    {
        return $this->scopeType;
    }

    public function getVariableValueMap()
    {
        return $this->variableValueMap;
    }

    public function hasThis()
    {
        return $this->thisObject !== null;
    }

    public function getThis()
    {
        return $this->thisObject;
    }
}