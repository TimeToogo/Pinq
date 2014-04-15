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
     * The expressions in this expression tree
     *
     * @var O\Expression[]
     */
    private $BodyExpressions = [];

    /**
     * @var O\ReturnExpression|null
     */
    private $ReturnValueExpressions = null;

    /**
     * @var Parsing\Walkers\VariableResolver
     */
    private $VariableResolver;

    /**
     * @var Parsing\Walkers\ValueUnresolver
     */
    private $ValueUnresolver;

    /**
     * @var Parsing\Walkers\ReturnValueExpressionResolver
     */
    private $ReturnValueExpressionResolver;

    /**
     * @var string|null
     */
    private $SerializedData = null;

    /**
     * @var callable|null
     */
    private $CompiledFunction = null;

    public function __construct(callable $OriginalFunction = null, array $ParameterExpressions, array $Expressions)
    {
        $this->ParameterExpressions = $ParameterExpressions;
        
        $this->VariableResolver = new O\Walkers\VariableResolver();
        $this->ReturnValueExpressionResolver = new O\Walkers\ReturnValueExpressionResolver();
        
        //Only parameterize objects, arrays and resources
        $this->ValueUnresolver = new O\Walkers\ValueUnresolver(function ($Value) { return !is_scalar($Value); });
        
        
        $this->Invalidate($Expressions);

        $this->CompiledFunction = $OriginalFunction;
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
            
            $Code = O\Expression::Closure(
                    $this->ParameterExpressions,
                    array_keys($ParameterNameValueMap),
                    $ParameterizedBodyExpressions)
                            ->Compile();
        
            $this->EvaluateFunctionCode($Code, $ParameterNameValueMap);
            
            if(!($this->CompiledFunction instanceof \Closure)) {
                throw new PinqException('Could not compile code into closure: %s', $Code);
            }
        }

        return $this->CompiledFunction;
    }
    
    private function EvaluateFunctionCode($___Code____00987654321, array $___UsedVariableNameValueMap____1234567890)
    {
        extract($___UsedVariableNameValueMap____1234567890);
        eval('$this->CompiledFunction = ' . $___Code____00987654321 . ';');
    }

    /**
     * @return O\Expression[]
     */
    final public function GetExpressions()
    {
        return $this->BodyExpressions;
    }

    /**
     * @return O\ReturnExpression
     * @throws Parsing\InvalidFunctionException
     */
    final public function GetFirstResolvedReturnValueExpression()
    {
        if(count($this->ReturnValueExpressions) === 0) {
            throw Parsing\InvalidFunctionException::MustContainValidReturnExpression(Reflection::FromCallable($this->CompiledFunction));
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

    final protected function Invalidate(array $NewBodyExpressions, $WalkUnresolvedVariables = true)
    {
        if ($this->BodyExpressions === $NewBodyExpressions) {
            return;
        }
        
        $this->SerializedData = null;
        $this->CompiledFunction = null;
        
        $this->BodyExpressions = $NewBodyExpressions;
        
        if($WalkUnresolvedVariables) {
            $this->VariableResolver->ResetUnresolvedVariables();
            $this->BodyExpressions = $this->VariableResolver->WalkAll($this->BodyExpressions);
        }
        
        $this->ReturnValueExpressionResolver->ResetReturnExpressions();
        $this->ReturnValueExpressionResolver->WalkAll($this->BodyExpressions);
        $this->ReturnValueExpressions = $this->ReturnValueExpressionResolver->GetResolvedReturnValueExpression();
        
    }

    final public function Walk(O\ExpressionWalker $ExpressionWalker)
    {
        $this->Invalidate($ExpressionWalker->WalkAll($this->BodyExpressions));
    }

    final public function Simplify()
    {
        $this->Invalidate(O\Expression::SimplifyAll($this->BodyExpressions));
    }

    final public function HasUnresolvedVariables()
    {
        return $this->VariableResolver->HasUnresolvedVariables();
    }

    final public function GetUnresolvedVariables()
    {
        return $this->VariableResolver->GetUnresolvedVariables();
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
        $this->VariableResolver->ResetUnresolvedVariables();
        $this->VariableResolver->SetVariableExpressionMap($VariableExpressionMap);
        $this->Invalidate($this->VariableResolver->WalkAll($this->BodyExpressions), false);
    }
}
