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
    private $ReturnExpression = null;

    /**
     * @var Parsing\Walkers\VariableResolver
     */
    private $VariableResolverWalker;

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
        $this->VariableResolverWalker = new Parsing\Walkers\VariableResolver();
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
            $Code = O\Expression::Closure($this->ParameterExpressions, [], $this->BodyExpressions)
                    ->Compile();
            
            $this->CompiledFunction  = eval('return ' . $Code . ';');
            if(!($this->CompiledFunction instanceof \Closure)) {
                throw new PinqException('Could not compile code into closure: %s', $Code);
            }
        }

        return $this->CompiledFunction;
    }

    /**
     * @return O\Expression[]
     */
    final public function GetExpressions()
    {
        return $this->BodyExpressions;
    }

    /**
     * @return boolean
     */
    final public function HasReturnExpression()
    {
        return $this->ReturnExpression !== null;
    }

    /**
     * @return O\ReturnExpression|null
     */
    final public function GetReturnExpression()
    {
        return $this->ReturnExpression;
    }

    /**
     * @return O\ReturnExpression
     */
    final public function VerifyReturnExpression()
    {
        if($this->ReturnExpression === null) {
            throw InvalidFunctionException::MustContainValidReturnExpression(Reflection::FromCallable($this->CompiledFunction));
        }
        return $this->ReturnExpression;
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
        $this->BodyExpressions = $NewBodyExpressions;
        if($WalkUnresolvedVariables) {
            $this->VariableResolverWalker->ResetUnresolvedVariables();
            $this->VariableResolverWalker->WalkAll($this->BodyExpressions);
        }
        $this->LoadReturnExpression();
        $this->CompiledFunction = null;
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
        return $this->VariableResolverWalker->HasUnresolvedVariables();
    }

    final public function GetUnresolvedVariables()
    {
        return $this->VariableResolverWalker->GetUnresolvedVariables();
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
        $this->VariableResolverWalker->ResetUnresolvedVariables();
        $this->VariableResolverWalker->SetVariableExpressionMap($VariableExpressionMap);
        $this->Invalidate($this->VariableResolverWalker->WalkAll($this->BodyExpressions), false);
    }

    final protected function LoadReturnExpression()
    {
        $this->ReturnExpression = null;
        foreach ($this->BodyExpressions as $Expression) {
            if ($Expression instanceof O\ReturnExpression) {
                $this->ReturnExpression = $Expression;
            }
        }
    }
}
