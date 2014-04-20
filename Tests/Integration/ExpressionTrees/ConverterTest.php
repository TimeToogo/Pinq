<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use \Pinq\Parsing\IFunctionToExpressionTreeConverter;
use \Pinq\Expressions as O;

abstract class ConverterTest extends \Pinq\Tests\PinqTestCase
{
    private $Implementations;
    
    /**
     * @var IFunctionToExpressionTreeConverter
     */
    private $CurrentImplementation;
    
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->Implementations = $this->Implementations();
        
        $this->CurrentImplementation = isset($data[0]) ? $data[0] : null;
    }

    protected function Implementations() 
    {
        return [
            new \Pinq\Parsing\FunctionToExpressionTreeConverter(new \Pinq\Parsing\PHPParser\Parser()),
        ];
    }

    final public function Converters() 
    {
        return array_map(function ($I) { return [$I]; }, $this->Implementations);
    }
    
    private function VerifyImplementation()
    {
        if($this->CurrentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }
    }
    
    final protected function AssertConvertsAndRecompilesCorrectly(callable $Function, array $ArgumentSets, O\Expression $ReturnExpression = null, $VerifySerialization = true) 
    {
        $this->VerifyImplementation();
        $FunctionExpressionTree = $this->CurrentImplementation->Convert($Function);
        //Ensure that function is recompiled
        $FunctionExpressionTree->SetCompiledFunction(null);
        
        if(empty($ArgumentSets)) {
            $ArgumentSets = [[]];
        }
        foreach ($ArgumentSets as $ArgumentSet) {
            $ExpectedReturn = call_user_func_array($Function, $ArgumentSet);
            $ActualReturn = call_user_func_array($FunctionExpressionTree, $ArgumentSet);
            
            $this->assertEquals($ExpectedReturn, $ActualReturn, 
                    'Should return equivalent results for arguments: ' . implode(', ', array_map(function ($I) { return var_export($I, true); }, $ArgumentSet)));
        }
        
        if($ReturnExpression !== null) {
            $this->AssertFirstResolvedReturnExpression($Function, $ReturnExpression);
        }
        if($VerifySerialization) {
            $this->AssertSerializesAndUnserializedCorrectly($FunctionExpressionTree);
        }
    }
    
    private function AssertSerializesAndUnserializedCorrectly(\Pinq\FunctionExpressionTree $FunctionExpressionTree) 
    {
        //Don't bother serializing the whole of PHPUnit...
        $FunctionExpressionTree->ResolveVariables(['this' => null]);
        
        $SerializedFunctionExpressionTree = unserialize(serialize($FunctionExpressionTree));
        
        $this->assertEquals($FunctionExpressionTree->GetParameterExpressions(), $SerializedFunctionExpressionTree->GetParameterExpressions());
        $this->assertTrue($FunctionExpressionTree->GetCompiledCode() == $SerializedFunctionExpressionTree->GetCompiledCode());
        $this->assertEquals($FunctionExpressionTree->GetExpressions(), $SerializedFunctionExpressionTree->GetExpressions());
        $this->assertEquals($FunctionExpressionTree->GetUnresolvedVariables(), $SerializedFunctionExpressionTree->GetUnresolvedVariables());
        if($FunctionExpressionTree->HasReturnExpression()) {
            $this->assertEquals($FunctionExpressionTree->GetFirstResolvedReturnValueExpression(), $SerializedFunctionExpressionTree->GetFirstResolvedReturnValueExpression());
        }
        $this->assertEquals($FunctionExpressionTree->HasReturnExpression(), $SerializedFunctionExpressionTree->HasReturnExpression());
    }
    
    final protected function AssertFirstResolvedReturnExpression(callable $Function, O\Expression $Expression) 
    {
        $this->VerifyImplementation();
        $FunctionExpressionTree = $this->CurrentImplementation->Convert($Function);
        
        $this->assertEquals($Expression->Simplify(), $FunctionExpressionTree->GetFirstResolvedReturnValueExpression());
    }
    
    final protected function AssertParametersAre(callable $Function, array $ParameterExpresssions) 
    {
        $this->VerifyImplementation();
        $FunctionExpressionTree = $this->CurrentImplementation->Convert($Function);
        
        $this->assertEquals($FunctionExpressionTree->GetParameterExpressions(), $ParameterExpresssions);
    }
    
    final protected function AssertUnresolvedVariablesAre(callable $Function, array $UnresolvedVariables) 
    {
        $this->VerifyImplementation();
        $FunctionExpressionTree = $this->CurrentImplementation->Convert($Function);
        
        $this->assertEquals(array_values($FunctionExpressionTree->GetUnresolvedVariables()), array_values($UnresolvedVariables));
    }
}
