<?php

namespace Pinq;

use \Pinq\Expressions as O;

/**
 * Acts as a mutable container and compiler for the underlying expression tree of a function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionExpressionTree implements \Serializable
{
    /**
     * @var O\ParameterExpression[]
     */
    private $ParameterExpressions;

    /**
     * The expressions of the body statements of the function
     *
     * @var O\Expression[]
     */
    private $BodyExpressions = [];

    /**
     * The body expressions with resolved sub queries
     *
     * @var O\Expression[]
     */
    private $BodyExpressionsWithSubQueries = [];

    /**
     * Expressions which can resolve to subqueries
     *
     * @var O\Expression[]
     */
    private $QueryableExpressions = [];

    /**
     * @var O\ReturnExpression[]
     */
    private $ReturnValueExpressions = [];

    /**
     * @var O\Walkers\VariableResolver
     */
    private $VariableResolver;

    /**
     * @var O\Walkers\UnresolvedVariableFinder
     */
    private $UnresolvedVariableFinder;

    /**
     * @var O\Walkers\ValueUnresolver
     */
    private $ValueUnresolver;

    /**
     * @var O\Walkers\ReturnValueExpressionResolver
     */
    private $ReturnValueExpressionResolver;

    /**
     * @var O\Walkers\SubQueryResolver
     */
    private $SubQueryResolver;

    /**
     * @var string[]
     */
    private $UnresolvedVariables = [];

    /**
     * @var string|null
     */
    private $SerializedData = null;

    /**
     * @var string|null
     */
    private $CompiledCode = null;

    /**
     * @var callable|null
     */
    private $CompiledFunction = null;

    public function __construct(callable $OriginalFunction = null, array $ParameterExpressions, array $Expressions)
    {
        $this->ParameterExpressions = $ParameterExpressions;
        
        $this->VariableResolver = new O\Walkers\VariableResolver();
        $this->UnresolvedVariableFinder = new O\Walkers\UnresolvedVariableFinder();
        $this->ReturnValueExpressionResolver = new O\Walkers\ReturnValueExpressionResolver();
        
        //Only parameterize objects, arrays and resources. Filter cannot be closure due to serialization.
        $this->ValueUnresolver = new O\Walkers\ValueUnresolver([__CLASS__, 'IsNotScalar']);
        
        $this->SubQueryResolver = new O\Walkers\SubQueryResolver();
        
        $this->Invalidate($Expressions);

        $this->CompiledFunction = $OriginalFunction;
    }
    
    public static function IsNotScalar($Value) 
    {
        return !is_scalar($Value);
    }

    public static function FromClosureExpression(O\ClosureExpression $Expression, callable $OriginalFunction = null)
    {
        return new self(
                $OriginalFunction,
                $Expression->GetParameterExpressions(),
                $Expression->GetBodyExpressions());
    }
    
    public function SetCompiledFunction(callable $Function = null) {
        $this->CompiledFunction = $Function;
    }
    
    /**
     * @return string
     */
    public function GetCompiledCode()
    {
        $this->LoadCompiledFunction();
        return $this->CompiledCode;
    }
    
    /**
     * @return callable
     */
    public function GetCompiledFunction()
    {
        return $this->LoadCompiledFunction();
    }
    
    public function serialize()
    {
        if($this->SerializedData === null) {
            $DataToSerialize = get_object_vars($this);
            
            unset($DataToSerialize['SerializedData']);
            unset($DataToSerialize['CompiledFunction']);
            
            $this->SerializedData = serialize($DataToSerialize);
        }
        
        return $this->SerializedData;
    }
    
    public function unserialize($Serialized)
    {
        foreach(unserialize($Serialized) as $PropertyName => $Value) {
            $this->$PropertyName = $Value;
        }
        
        $this->SerializedData = $Serialized;
    }
    
    public function __clone()
    {
        foreach($this->BodyExpressions as $Key => $BodyExpression) {
            $this->BodyExpressions[$Key] = clone $BodyExpression;
        }
        foreach($this->BodyExpressionsWithSubQueries as $Key => $BodyExpression) {
            $this->BodyExpressionsWithSubQueries[$Key] = clone $BodyExpression;
        }
        foreach($this->ParameterExpressions as $Key => $ParameterExpressions) {
            $this->ParameterExpressions[$Key] = clone $ParameterExpressions;
        }
        foreach($this->ReturnValueExpressions as $Key => $ReturnExpression) {
            $this->ReturnValueExpressions[$Key] = clone $ReturnExpression;
        }
        $this->CompiledFunction = $this->CompiledFunction === null ? null : clone $this->CompiledFunction;
        $this->ReturnValueExpressionResolver = clone $this->ReturnValueExpressionResolver;
        $this->ValueUnresolver = clone $this->ValueUnresolver;
        $this->VariableResolver = clone $this->VariableResolver;
    }

    public function __invoke()
    {
        $Function = $this->LoadCompiledFunction();
        
        return call_user_func_array($Function, func_get_args());
    }
    
    private function LoadCompiledFunction()
    {
        if ($this->CompiledFunction === null) {
            $this->ValueUnresolver->ResetVariableNameValueMap();
            $ParameterizedBodyExpressions = $this->ValueUnresolver->WalkAll($this->BodyExpressions);
            $ParameterNameValueMap = $this->ValueUnresolver->GetVariableNameValueMap();
            
            if($this->CompiledCode === null) {
                $this->CompiledCode = O\Expression::Closure(
                        $this->ParameterExpressions,
                        array_keys($ParameterNameValueMap),
                        $ParameterizedBodyExpressions)
                                ->Compile();
            }
        
            $this->EvaluateFunctionCode($this->CompiledCode, $ParameterNameValueMap);
            
            if(!($this->CompiledFunction instanceof \Closure)) {
                throw new PinqException('Could not compile code into closure: %s', $this->CompiledCode);
            }
        }
        
        return $this->CompiledFunction;
    }
    
    /**
     * @param string $___Code____00987654321
     */
    private function EvaluateFunctionCode($___Code____00987654321, array $___UsedVariableNameValueMap____1234567890)
    {
        extract($___UsedVariableNameValueMap____1234567890);
        eval('$this->CompiledFunction = ' . $___Code____00987654321 . ';');
    }

    /**
     * Gets the body expressions
     * 
     * @return O\Expression[]
     */
    final public function GetExpressions()
    {
        return $this->BodyExpressionsWithSubQueries;
    }

    /**
     * @return boolean
     */
    final public function HasReturnExpression()
    {
        return count($this->ReturnValueExpressions) > 0;
    }
    
    /**
     * @return O\ReturnExpression
     * @throws Parsing\InvalidFunctionException
     */
    final public function GetFirstResolvedReturnValueExpression()
    {
        if(count($this->ReturnValueExpressions) === 0) {
            throw Parsing\InvalidFunctionException::MustContainValidReturnExpression(Parsing\Reflection::FromCallable($this->GetCompiledFunction()));
        }
        return $this->ReturnValueExpressions[0];
    }
    
    /**
     * @return O\ParameterExpression[]
     */
    final public function GetParameterExpressions()
    {
        return $this->ParameterExpressions;
    }

    final public function Walk(O\ExpressionWalker $ExpressionWalker)
    {
        $this->Invalidate($ExpressionWalker->WalkAll($this->BodyExpressions));
        
        return $this;
    }
    
    final public function Simplify()
    {
        $this->Invalidate(O\Expression::SimplifyAll($this->BodyExpressions));
        
        return $this;
    }

    final public function HasUnresolvedVariables()
    {
        return !empty($this->UnresolvedVariables);
    }

    final public function GetUnresolvedVariables()
    {
        return $this->UnresolvedVariables;
    }

    final public function ResolveVariables(array $VariableValueMap, array $VariableExpressionMap = [])
    {
        foreach ($VariableValueMap as $VariableName => $Value) {
            $VariableValueMap[$VariableName] = O\Expression::Value($Value);
        }

        $this->ResolveVariablesToExpressions($VariableExpressionMap + $VariableValueMap);
    }

    final public function ResolveVariablesToExpressions(array $VariableExpressionMap)
    {
        $this->VariableResolver->SetVariableExpressionMap($VariableExpressionMap);
        $this->Invalidate($this->VariableResolver->WalkAll($this->BodyExpressions));
    }

    final public function SetQueryableExpressions(array $Expressions)
    {
        $this->QueryableExpressions = $Expressions;
        $this->Invalidate($this->BodyExpressions);
    }

    final protected function Invalidate(array $NewBodyExpressions)
    {
        if ($this->BodyExpressions === $NewBodyExpressions) {
            return;
        }
        
        $this->BodyExpressions = $NewBodyExpressions;
        
        $this->SerializedData = null;
        $this->CompiledCode = null;
        $this->CompiledFunction = null;
        
        $this->LoadUnresolvedVariables();
        
        $this->LoadResolvedReturnExpressions();
        
        $this->LoadResolvedSubQueriesExpressions();
    }
    
    private function LoadUnresolvedVariables()
    {
        $this->UnresolvedVariableFinder->ResetUnresolvedVariables();
        $this->UnresolvedVariableFinder->WalkAll($this->BodyExpressions);
        
        $ParameterNames = [];
        foreach ($this->ParameterExpressions as $ParameterExpression) {
            $ParameterNames[] = $ParameterExpression->GetName();
        }
        
        $this->UnresolvedVariables = array_diff($this->UnresolvedVariableFinder->GetUnresolvedVariables(), $ParameterNames); 
    }
    
    private function LoadResolvedReturnExpressions()
    {
        $this->ReturnValueExpressionResolver->ResetReturnExpressions();
        $this->ReturnValueExpressionResolver->WalkAll($this->BodyExpressions);
        
        $this->ReturnValueExpressions = $this->ReturnValueExpressionResolver->GetResolvedReturnValueExpression();
        $this->ReturnValueExpressions = $this->ResolveSubQueries($this->ReturnValueExpressions);
    }
    
    
    private function LoadResolvedSubQueriesExpressions()
    {
        $this->BodyExpressionsWithSubQueries = $this->ResolveSubQueries($this->BodyExpressions);
    }
    
    private function ResolveSubQueries(array $Expressions)
    {
        $this->SubQueryResolver->SetFilter(function (O\MethodCallExpression $Expression) {
            $ValueExpression = $Expression->GetValueExpression();
            
            if(in_array($ValueExpression, $this->QueryableExpressions)) {
                return true;
            }
            else if($ValueExpression instanceof O\ValueExpression && $ValueExpression->GetValue() instanceof ITraversable) {
                return true;
            }
            else if($ValueExpression instanceof O\VariableExpression && $ValueExpression->GetNameExpression() instanceof O\ValueExpression) {
                $VariableName = $ValueExpression->GetNameExpression()->GetValue();
                foreach($this->ParameterExpressions as $ParameterExpression) {
                    if($ParameterExpression->HasTypeHint() 
                            && is_a($ParameterExpression->GetTypeHint(), ITraversable::ITraversableType, true)
                            && $ParameterExpression->GetName() === $VariableName) {
                        return true;
                    }
                }
            }
            
            return false;
        });
        
        $ResolvedExpressions = $this->SubQueryResolver->WalkAll($Expressions);
        
        $this->SubQueryResolver->SetFilter(null);
        
        return $ResolvedExpressions;
    }
}
