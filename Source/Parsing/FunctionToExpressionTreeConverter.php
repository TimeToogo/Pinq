<?php

namespace Pinq\Parsing;

use \Pinq\Expressions as O;
use \Pinq\Caching\Provider;
use \Pinq\Caching\IFunctionCache;

class FunctionToExpressionTreeConverter implements IFunctionToExpressionTreeConverter
{
    /**
     * @var IParser
     */
    protected $Parser;
    
    /**
     * @var IFunctionCache 
     */
    private $Cache;

    public function __construct(IParser $Parser, IFunctionCache $Cache = null)
    {
        $this->Parser = $Parser;
        $this->Cache = $Cache ?: Provider::GetCache();
    }

    final public function GetParser()
    {
        return $this->Parser;
    }
    
    private function GetFunctionHash(\ReflectionFunctionAbstract $Reflection)
    {
        return md5($this->Parser->GetSignatureHash($Reflection) . '-' . $Reflection->getFileName());
    }
    
    /**
     * @return \Pinq\FunctionExpressionTree
     */
    final public function Convert(callable $Function)
    {
        if($Function instanceof \Pinq\FunctionExpressionTree) {
            return $Function;
        }
        
        $Reflection = Reflection::FromCallable($Function);
        
        $FullFunctionHash = $this->GetFunctionHash($Reflection);
        
        $ExpressionTree = $this->Cache->TryGet($FullFunctionHash);
        
        if (!($ExpressionTree instanceof \Pinq\FunctionExpressionTree)) {
            $ExpressionTree = $this->GetFunctionExpressionTree($Reflection);

            /*
             * Resolve all that can be currently resolved and save the expression tree with all
             * the unresolvable variables so it can be resolved with different values later
             */
            $ExpressionTree->Simplify();
            
            //Force code to compile for optimal cached state
            $ExpressionTree->GetCompiledCode();
            
            $this->Cache->Save($FullFunctionHash, $ExpressionTree);
        }
        //Set the compiled function with the original to prevent having to eval
        $ExpressionTree->SetCompiledFunction($Function);
        
        if ($ExpressionTree->HasUnresolvedVariables()) {
            /*
             * Simplify and resolve any remaining expressions that could not be resolved due
             * to unresolved variables
             */
            $this->Resolve($ExpressionTree, $this->GetKnownVariables($Reflection, $Function), []);
        }

        return $ExpressionTree;
    }
    
    protected function GetKnownVariables(\ReflectionFunctionAbstract $Reflection, callable $Function)
    {
        //ReflectionFunction::getStaticVariables() returns the used variables for closures
        $KnownVariables = $Reflection->getStaticVariables();
        
        
        //HHVM Compatibility: hhvm does not support ReflectionFunctionAbstract::getClosureThis
        if($Function instanceof \Closure && !defined('HHVM_VERSION')) {
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
            $ArgumentExpressions[] = O\Expression::Variable(O\Expression::Value($Parameter->getName()));
        }
        
        if(!$HasUnavailableDefaultValue) {
            return [
                O\Expression::ReturnExpression(
                        O\Expression::FunctionCall(
                                O\Expression::Value($Reflection->getName()),
                                $ArgumentExpressions))
            ];
        }
        else {
            return [
                O\Expression::ReturnExpression(
                        O\Expression::FunctionCall(O\Expression::Value('call_user_func_array'),
                                [O\Expression::Value($Reflection->getName()), O\Expression::FunctionCall(O\Expression::Value('func_get_args'))]))
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
            $TypeHint = $Parameter->getClass()->getName();
        }
        
        return O\Expression::Parameter(
                $Parameter->getName(), 
                $TypeHint, 
                $Parameter->isOptional(), 
                $Parameter->isDefaultValueAvailable() ? $Parameter->getDefaultValue() : null, 
                $Parameter->isPassedByReference());
    }
}
