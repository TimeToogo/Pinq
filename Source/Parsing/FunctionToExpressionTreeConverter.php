<?php

namespace Pinq\Parsing;

class FunctionToExpressionTreeConverter implements IFunctionToExpressionTreeConverter
{
    /**
     * @var Functions\IParser
     */
    protected $Parser;

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
                $this->GetParameterNameTypeHintMap($Reflection),
                $this->Parser->Parse($Reflection)->GetExpressions(),
                $Function);
    }

    /**
     * @return \Pinq\FunctionExpressionTree
     */
    final public function Convert(callable $Function)
    {
        if($Function instanceof \Pinq\FunctionExpressionTree) {
            return $Function;
        }
        
        return $this->ConvertAndResolve($Function, Reflection::FromCallable($Function));
    }
    
    protected function ConvertAndResolve(callable $Function, \ReflectionFunctionAbstract $Reflection) {
        $ExpressionTree = $this->GetFunctionExpressionTree($Reflection);

        //ReflectionFunction::getStaticVariables() returns the used variables for closures
        $this->Resolve($ExpressionTree, $Reflection->getStaticVariables(), []);

        if ($ExpressionTree->HasUnresolvedVariables()) {
            throw InvalidFunctionException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }

        return $ExpressionTree;
    }

    final protected function Resolve(
            \Pinq\FunctionExpressionTree $ExpressionTree,
            array $VariableValueMap,
            array $VariableExpressionMap) {
        $ExpressionTree->ResolveVariables($VariableValueMap, $VariableExpressionMap);
        $ExpressionTree->Simplify();
    }
}
