<?php 

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Parsing\IFunctionToExpressionTreeConverter;
use Pinq\Expressions as O;

abstract class ConverterTest extends \Pinq\Tests\PinqTestCase
{
    private $implementations;
    
    /**
     * @var IFunctionToExpressionTreeConverter
     */
    private $currentImplementation;
    
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->implementations = $this->implementations();
        $this->currentImplementation = isset($data[0]) ? $data[0] : null;
    }
    
    protected function implementations()
    {
        return [new \Pinq\Parsing\FunctionToExpressionTreeConverter(new \Pinq\Parsing\PHPParser\Parser())];
    }
    
    public final function converters()
    {
        return array_map(function ($i) {
            return [$i];
        }, $this->implementations);
    }
    
    private function verifyImplementation()
    {
        if ($this->currentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }
    }
    
    protected final function assertConvertsAndRecompilesCorrectly(callable $function, array $argumentSets, O\Expression $returnExpression = null, $verifySerialization = true)
    {
        $this->verifyImplementation();
        $functionExpressionTree = $this->currentImplementation->convert($function);
        //Ensure that function is recompiled
        $functionExpressionTree->setCompiledFunction(null);
        
        if (empty($argumentSets)) {
            $argumentSets = [[]];
        }
        
        foreach ($argumentSets as $argumentSet) {
            $expectedReturn = call_user_func_array($function, $argumentSet);
            $actualReturn = call_user_func_array($functionExpressionTree, $argumentSet);
            
            $this->assertEquals(
                    $expectedReturn,
                    $actualReturn,
                    'Should return equivalent results for arguments: ' . 
                            implode(', ', array_map(function ($i) {
                                return var_export($i, true);
                            }, $argumentSet)));
        }
        
        if ($returnExpression !== null) {
            $this->assertFirstResolvedReturnExpression(
                    $function,
                    $returnExpression);
        }
        
        if ($verifySerialization) {
            $this->assertSerializesAndUnserializedCorrectly($functionExpressionTree);
        }
    }
    
    private function assertSerializesAndUnserializedCorrectly(\Pinq\FunctionExpressionTree $functionExpressionTree)
    {
        //Don't bother serializing the whole of PHPUnit...
        $functionExpressionTree->resolveVariables(['this' => null]);
        
        $serializedFunctionExpressionTree = unserialize(serialize($functionExpressionTree));
        
        $this->assertEquals(
                $functionExpressionTree->getParameterExpressions(),
                $serializedFunctionExpressionTree->getParameterExpressions());
        
        $this->assertTrue($functionExpressionTree->getCompiledCode() == $serializedFunctionExpressionTree->getCompiledCode());
        
        $this->assertEquals(
                $functionExpressionTree->getExpressions(),
                $serializedFunctionExpressionTree->getExpressions());
        
        $this->assertEquals(
                $functionExpressionTree->getUnresolvedVariables(),
                $serializedFunctionExpressionTree->getUnresolvedVariables());
        
        if ($functionExpressionTree->hasReturnExpression()) {
            $this->assertEquals(
                    $functionExpressionTree->getFirstResolvedReturnValueExpression(),
                    $serializedFunctionExpressionTree->getFirstResolvedReturnValueExpression());
        }
        
        $this->assertEquals(
                $functionExpressionTree->hasReturnExpression(),
                $serializedFunctionExpressionTree->hasReturnExpression());
    }
    
    protected final function assertFirstResolvedReturnExpression(callable $function, O\Expression $expression)
    {
        $this->verifyImplementation();
        
        $functionExpressionTree = $this->currentImplementation->convert($function);
        
        $this->assertEquals(
                $expression->simplify(),
                $functionExpressionTree->getFirstResolvedReturnValueExpression());
    }
    
    protected final function assertParametersAre(callable $function, array $parameterExpresssions)
    {
        $this->verifyImplementation();
        
        $functionExpressionTree = $this->currentImplementation->convert($function);
        
        $this->assertEquals(
                $functionExpressionTree->getParameterExpressions(),
                $parameterExpresssions);
    }
    
    protected final function assertUnresolvedVariablesAre(callable $function, array $unresolvedVariables)
    {
        $this->verifyImplementation();
        
        $functionExpressionTree = $this->currentImplementation->convert($function);
        
        $this->assertEquals(
                array_values($functionExpressionTree->getUnresolvedVariables()),
                array_values($unresolvedVariables));
    }
}