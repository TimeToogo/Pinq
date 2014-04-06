<?php

namespace Pinq\Parsing;

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

    final protected function GetParameterNameTypeHintMap(\ReflectionFunctionAbstract $Reflection)
    {
        $ParameterNameTypeHintMap = [];
        foreach ($Reflection->getParameters() as $Parameter) {
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

            $ParameterNameTypeHintMap[$Parameter->name] = $TypeHint;
        }

        return $ParameterNameTypeHintMap;
    }

    /**
     * @param  \ReflectionFunctionAbstract $Reflection
     * @return \Pinq\FunctionExpressionTree
     */
    final protected function GetFunctionExpressionTree(
            \ReflectionFunctionAbstract $Reflection, callable $Function = null) {
        return new \Pinq\FunctionExpressionTree(
                $Function,
                $this->GetParameterNameTypeHintMap($Reflection),
                $this->Parser->Parse($Reflection));
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
