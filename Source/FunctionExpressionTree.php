<?php

namespace Pinq;

use \Pinq\Expressions as O;

/**
 * Acts as a mutable container and compiler for the underlying expression tree of a function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FunctionExpressionTree
{
    /**
     * @var array
     */
    private $ParamterNameTypeHintMap;

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

    private $VariableResolverWalker;

    /**
     * @var callable|null
     */
    private $CompiledFunction = null;

    public function __construct(callable $OriginalFunction = null, array $ParamterNameTypeHintMap, array $Expressions)
    {
        $this->ParamterNameTypeHintMap = $ParamterNameTypeHintMap;
        $this->Invalidate($Expressions);

        $this->VariableResolverWalker = new Parsing\Walkers\VariableResolverWalker();
        $this->CompiledFunction = $OriginalFunction;
    }

    public static function FromClosureExpression(O\ClosureExpression $Expression, callable $OriginalFunction = null)
    {
        return new self(
                $OriginalFunction,
                $Expression->GetParameterNameTypeHintMap(),
                $Expression->GetBodyExpressions());
    }
    
    public function SetOriginalFunction(callable $Function) {
        $this->CompiledFunction = $Function;
    }

    public function __invoke()
    {
        $Function = $this->LoadCompiledFunction();
        
        return call_user_func_array($Function, func_get_args());
    }

    private function LoadCompiledFunction()
    {
        if ($this->CompiledFunction === null) {
            $Code = O\Expression::Closure($this->ParamterNameTypeHintMap, [], $this->BodyExpressions)
                    ->Compile();
            
            $this->CompiledFunction  = eval('$Closure = ' . $Code . '; return $Closure;');
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
     * @return array
     */
    final public function GetParameterNameTypehintMap()
    {
        return $this->ParamterNameTypeHintMap;
    }

    final protected function Invalidate(array $NewBodyExpressions)
    {
        if ($this->BodyExpressions === $NewBodyExpressions) {
            return;
        }
        
        $this->BodyExpressions = $NewBodyExpressions;
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
        $this->VariableResolverWalker->SetVariableResolutionMap($VariableExpressionMap);
        $this->Invalidate($this->VariableResolverWalker->WalkAll($this->BodyExpressions));
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
