<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Base class of a function structure.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Base implements \Serializable
{
    /**
     * @var string
     */
    protected $callableId;

    /**
     * @var string|null
     */
    protected $scopeType;

    /**
     * Array containing the scoped variable names of the function indexed
     * by their respective parameter names.
     *
     * @var array<string, string>
     */
    protected $parameterScopedVariableMap;

    /**
     * The structure of the function's parameters
     *
     * @var Parameters\Base
     */
    protected $parameters;

    /**
     * The expressions of the body statements of the function
     *
     * @var O\Expression[]||null
     */
    protected $bodyExpressions = [];

    final public function __construct(
            $callableId,
            $scopeType,
            array $parameterScopedVariableMap,
            array $parameterExpressions,
            array $bodyExpressions = null
    ) {
        $this->callableId                 = $callableId;
        $this->scopeType                  = $scopeType;
        $this->parameterScopedVariableMap = $parameterScopedVariableMap;
        $this->parameters                 = $this->getParameterStructure($parameterExpressions);
        $this->bodyExpressions            = $bodyExpressions;

        $this->initialize();
    }

    protected function initialize()
    {

    }

    /**
     * @param O\ParameterExpression[] $parameterExpressions
     *
     * @return Parameters\Base
     */
    abstract function getParameterStructure(array $parameterExpressions);

    /**
     * Gets a callable factory for the function structure.
     *
     * @return callable
     */
    public static function factory()
    {
        $static = get_called_class();

        return function (
                $callableParameter,
                $scopeType,
                array $parameterScopedVariableMap,
                array $parameterExpressions,
                array $bodyExpressions = null
        ) use ($static) {
            return new $static(
                    $callableParameter,
                    $scopeType,
                    $parameterScopedVariableMap,
                    $parameterExpressions,
                    $bodyExpressions);
        };
    }

    /**
     * Gets the parameter id of the callable for the function.
     *
     * @return string
     */
    final public function getCallableId()
    {
        return $this->callableId;
    }

    /**
     * Whether the function has a scoped type.
     *
     * @return string|null
     */
    public function hasScopeType()
    {
        return $this->scopeType !== null;
    }

    /**
     * Gets the bound type of the function.
     * Null if there is no bound type.
     *
     * @return string|null
     */
    public function getScopeType()
    {
        return $this->scopeType;
    }

    /**
     * @return boolean
     */
    public function isInternal()
    {
        return $this->bodyExpressions === null;
    }

    final protected function verifyNotInternal()
    {
        if ($this->isInternal()) {
            throw new \Pinq\PinqException(
                    'Cannot get body expressions from %s: function is not user defined.',
                    get_class($this));
        }
    }

    /**
     * Gets an array containing the parameter ids and keys with their
     * respective scoped variable name as the value.
     *
     * @return array<string, string>
     */
    public function getParameterScopedVariableMap()
    {
        return $this->parameterScopedVariableMap;
    }

    final public function serialize()
    {
        return serialize(
                [
                        $this->callableId,
                        $this->scopeType,
                        $this->parameterScopedVariableMap,
                        $this->parameters,
                        $this->bodyExpressions,
                        $this->dataToSerialize()
                ]
        );
    }

    protected function dataToSerialize()
    {

    }

    final public function unserialize($data)
    {
        list(
                $this->callableId,
                $this->scopeType,
                $this->parameterScopedVariableMap,
                $this->parameters,
                $this->bodyExpressions,
                $data) = unserialize($data);
        $this->unserializeData($data);
    }

    protected function unserializeData($data)
    {

    }

    /**
     * Gets the body expressions of the function
     *
     * @return O\Expression[]
     * @throws \Pinq\PinqException if the function is internal
     */
    final public function getBodyExpressions()
    {
        $this->verifyNotInternal();
        return $this->bodyExpressions;
    }

    /**
     * Gets amount of body expressions of the function
     *
     * @return int
     * @throws \Pinq\PinqException if the function is internal
     */
    final public function countBodyExpressions()
    {
        $this->verifyNotInternal();
        return count($this->bodyExpressions);
    }

    /**
     * @return Parameters\Base
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
