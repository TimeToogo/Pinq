<?php

namespace Pinq\Expressions;

use Pinq\PinqException;

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
    protected $variableTable;

    public function __construct($namespace, $scopeType, $thisObject = null, array $variableTable = [])
    {
        $this->namespace     = $namespace;
        $this->scopeType     = $scopeType;
        $this->thisObject    = $thisObject;
        $this->variableTable = $variableTable;
        if (array_key_exists('this', $this->variableTable)) {
            throw new PinqException(
                    'Cannot create %s: invalid variable table, \'this\' variable is disallowed (use $thisObject constructor argument instead).',
                    get_class($this));
        }
    }

    /**
     * @param object|null $thisObject
     * @param array       $variableTable
     *
     * @return IEvaluationContext
     */
    public static function globalScope($thisObject = null, $variableTable = [])
    {
        return new self(null, null, $thisObject, $variableTable);
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

    public function getVariableTable()
    {
        return $this->variableTable;
    }

    public function hasThis()
    {
        return $this->thisObject !== null;
    }

    public function getThis()
    {
        return $this->thisObject;
    }

    public function withVariableTable(array $variableTable)
    {
        if ($this->variableTable === $variableTable) {
            return $this;
        }

        return new self($this->namespace, $this->scopeType, $this->thisObject, $variableTable);
    }
}
