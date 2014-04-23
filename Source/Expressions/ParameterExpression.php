<?php 

namespace Pinq\Expressions;

/**
 * <code>
 * function (\stdClass &$I = null) {}
 * </code>
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
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
     * @var boolean
     */
    private $hasDefaultValue;
    
    /**
     * @var mixed
     */
    private $defaultValue;
    
    /**
     * @var boolean
     */
    private $isPassedByReference;
    
    public function __construct($name, $typeHint = null, $hasDefaultValue = false, $defaultValue = null, $isPassedByReference = false)
    {
        $this->name = $name;
        $this->typeHint = $typeHint;
        $this->hasDefaultValue = $hasDefaultValue;
        $this->defaultValue = $defaultValue;
        $this->isPassedByReference = $isPassedByReference;
    }
    
    public function simplify()
    {
        return $this;
    }
    
    public function traverse(ExpressionWalker $walker)
    {
        return $walker->walkParameter($this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function hasTypeHint()
    {
        return $this->typeHint !== null;
    }
    
    public function getTypeHint()
    {
        return $this->typeHint;
    }
    
    public function hasDefaultValue()
    {
        return $this->hasDefaultValue;
    }
    
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
    
    public function isPassedByReference()
    {
        return $this->isPassedByReference;
    }
    
    public function update($name, $typeHint, $hasDefaultValue, $defaultValue, $isPassedByReference)
    {
        if ($this->name === $name && $this->typeHint === $typeHint && $this->hasDefaultValue === $hasDefaultValue && $this->defaultValue === $defaultValue && $this->isPassedByReference === $isPassedByReference) {
            return $this;
        }
        
        return new self(
                $name,
                $typeHint,
                $hasDefaultValue,
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
        
        if ($this->hasDefaultValue) {
            $code .= ' = ' . var_export($this->defaultValue, true);
        }
    }
    
    public function serialize()
    {
        return serialize([
            $this->defaultValue,
            $this->hasDefaultValue,
            $this->isPassedByReference,
            $this->name,
            $this->typeHint
        ]);
    }
    
    public function unserialize($serialized)
    {
        list($this->defaultValue, $this->hasDefaultValue, $this->isPassedByReference, $this->name, $this->typeHint) = unserialize($serialized);
    }
    
    public function __clone()
    {
        $this->defaultValue = is_object($this->defaultValue) ? clone $this->defaultValue : $this->defaultValue;
    }
}