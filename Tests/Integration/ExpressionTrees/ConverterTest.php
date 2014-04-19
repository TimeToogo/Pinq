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
    
    final protected function AssertConvertsAndRecompilesCorrectly(callable $Function, array $ArgumentSets, O\Expression $ReturnExpression = null) 
    {
        if($this->CurrentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }
        $FunctionExpressionTree = $this->CurrentImplementation->Convert($Function);
        //Ensure that function is recompiled
        $FunctionExpressionTree->SetCompiledFunction(null);
        
        if(empty($ArgumentSets)) {
            $ArgumentSets = [[]];
        }
        foreach ($ArgumentSets as $ArgumentSet) {
            $ExpectedReturn = call_user_func_array($Function, $ArgumentSet);
            $ActualReturn = call_user_func_array($FunctionExpressionTree, $ArgumentSet);
            
            $this->assertEquals($ExpectedReturn, $ActualReturn);
        }
        
        if($ReturnExpression !== null) {
            $this->AssertFirstResolvedReturnExpression($Function, $ReturnExpression);
        }
    }
    
    final protected function AssertFirstResolvedReturnExpression(callable $Function, O\Expression $Expression) 
    {
        if($this->CurrentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }
        $FunctionExpressionTree = $this->CurrentImplementation->Convert($Function);
        
        $this->assertEquals($Expression->Simplify(), $FunctionExpressionTree->GetFirstResolvedReturnValueExpression());
    }    
}
