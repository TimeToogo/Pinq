<?php

namespace Pinq\Parsing;

use \Pinq\Expressions as O;

class FunctionToExpressionTreeConverter implements IFunctionToExpressionTreeConverter
{
    /**
     * @var Functions\IParser
     */
    protected $Parser;
    
    /**
     * @var callable[]
     */
    protected $FunctionLookup = [];
    
    /**
     * @var \Pinq\FunctionExpressionTree[]
     */
    protected $ExpressionTreeCache = [];

    public function __construct(IParser $Parser)
    {
        $this->Parser = $Parser;
    }

    final public function GetParser()
    {
        return $this->Parser;
    }

    /**
     * @return \Pinq\FunctionExpressionTree
     */
    final protected function GetFunctionExpressionTree(\ReflectionFunctionAbstract $Reflection, callable $Function = null) 
    {
        
        $ParameterExpressions = $this->GetParameterExpressions($Reflection);
        $BodyExpressions = $Reflection->isUserDefined() ? 
                $this->Parser->Parse($Reflection) : $this->InternalFunctionExpressions($Reflection);
        
        return new \Pinq\FunctionExpressionTree(
                $Function,
                $ParameterExpressions,
                $BodyExpressions);
    }
    
    private function InternalFunctionExpressions(\ReflectionFunctionAbstract $Reflection) 
    {
        $HasUnavailableDefaultValue = false;
        $ArgumentExpressions = [];
        foreach($Reflection->getParameters() as $Parameter) {
            if($Parameter->isOptional() && !$Parameter->isDefaultValueAvailable()) {
                $HasUnavailableDefaultValue = true;
            }
            $ArgumentExpressions[] = O\Expression::Variable(O\Expression::Value($Parameter->name));
        }
        
        if(!$HasUnavailableDefaultValue) {
            return [
                O\Expression::ReturnExpression(
                        O\Expression::FunctionCall(
                                O\Expression::Value($Reflection->name),
                                $ArgumentExpressions))
            ];
        }
        else {
            return [
                O\Expression::ReturnExpression(
                        O\Expression::FunctionCall(O\Expression::Value('call_user_func_array'),
                                [O\Expression::Value($Reflection->name), O\Expression::FunctionCall(O\Expression::Value('func_get_args'))]))
            ];
        }
    }


    final protected function GetParameterExpressions(\ReflectionFunctionAbstract $Reflection)
    {
        $ParameterExpressions = [];
        
        foreach ($Reflection->getParameters() as $Parameter) {
            $ParameterExpressions[] = $this->GetParameterExpression($Parameter);
        }

        return $ParameterExpressions;
    }
    
    private function GetParameterExpression(\ReflectionParameter $Parameter) 
    {
        $TypeHint = null;
        if ($Parameter->isArray()) {
            $TypeHint = 'array';
        } 
        else if ($Parameter->isCallable()) {
            $TypeHint = 'callable';
        } 
        else if ($Parameter->getClass() !== null) {
            $TypeHint = $Parameter->getClass()->name;
        }
        
        return O\Expression::Parameter(
                $Parameter->name, 
                $TypeHint, 
                $Parameter->isOptional(), 
                $Parameter->isDefaultValueAvailable() ? $Parameter->getDefaultValue() : null, 
                $Parameter->isPassedByReference());
    }
    
    /**
     * @return \Pinq\FunctionExpressionTree
     */
    final public function Convert(callable $Function)
    {
        if($Function instanceof \Pinq\FunctionExpressionTree) {
            return $Function;
        }
        
        $Key = array_search($Function, $this->FunctionLookup, true);
        if($Key === false) {
            $Key = count($this->FunctionLookup) + 1;
            $this->ExpressionTreeCache[$Key] = $this->ConvertAndResolve($Function, Reflection::FromCallable($Function));
        }
        
        return clone $this->ExpressionTreeCache[$Key];
    }
    
    protected function ConvertAndResolve(callable $Function, \ReflectionFunctionAbstract $Reflection) {
        $ExpressionTree = $this->GetFunctionExpressionTree($Reflection, $Function);
        
        $this->Resolve($ExpressionTree, $this->GetKnownVariables($Reflection, $Function), []);

        return $ExpressionTree;
    }
    
    protected function GetKnownVariables(\ReflectionFunctionAbstract $Reflection, callable $Function)
    {
        //ReflectionFunction::getStaticVariables() returns the used variables for closures
        $KnownVariables = $Reflection->getStaticVariables();
        
        if($Function instanceof \Closure) {
            $ThisValue = $Reflection->getClosureThis();
            if($ThisValue !== null) {
                $KnownVariables['this'] = $ThisValue;
            }
        }
        else if (is_array($Function) && is_object($Function[0])) {
            $KnownVariables['this'] = $Function[0];
        }
        
        return $KnownVariables;
    }

    final protected function Resolve(
            \Pinq\FunctionExpressionTree $ExpressionTree,
            array $VariableValueMap,
            array $VariableExpressionMap) {
        $ExpressionTree->ResolveVariables($VariableValueMap, $VariableExpressionMap);
        $ExpressionTree->Simplify();
    }
}
