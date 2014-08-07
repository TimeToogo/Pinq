<?php

namespace Pinq\Tests\Integration\ExpressionTrees;

use Pinq\Parsing;
use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Expressions as O;

abstract class InterpreterTest extends \Pinq\Tests\PinqTestCase
{
    private $implementations;

    /**
     * @var IFunctionInterpreter
     */
    protected $currentImplementation;

    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->implementations = $this->implementations();
        $this->currentImplementation = isset($data[0]) ? $data[0] : null;
    }

    protected function implementations()
    {
        return [Parsing\FunctionInterpreter::getDefault()];
    }

    final public function interpreters()
    {
        return array_map(function ($i) {
            return [$i];
        }, $this->implementations);
    }

    /**
     * @return IFunctionInterpreter
     */
    final protected function verifyImplementation()
    {
        if ($this->currentImplementation === null) {
            throw new \Exception('Please remember to use the @dataProvider annotation to test all the implementations.');
        }
        
        return $this->currentImplementation;
    }

    final protected function assertRecompilesCorrectly(
            callable $function,
            array $argumentSets = null,
            O\Expression $returnExpression = null, 
            $verifySerialization = true)
    {
        $interpreter = $this->verifyImplementation();
        
        $reflection = $interpreter->getReflection($function);
        $structure = $interpreter->getStructure($reflection);
        $recompiledFunction = $this->recompile($reflection, $structure, $closure);

        if (empty($argumentSets)) {
            $argumentSets = [[]];
        }

        foreach ($argumentSets as $argumentSet) {
            $expectedReturn = call_user_func_array($function, $argumentSet);
            $actualReturn = call_user_func_array($recompiledFunction, $argumentSet);

            $this->assertEquals(
                    $expectedReturn,
                    $actualReturn,
                    'Should return equivalent results for arguments: ' .
                            implode(', ', 
                                    array_map(
                                            function ($i) {return var_export($i, true); }, 
                                            $argumentSet))
                    . '|code: ' . $closure->compileDebug());
        }

        if ($returnExpression !== null) {
            $this->assertScopedVariables(
                    $function,
                    $returnExpression);
        }

        if ($verifySerialization) {
            $this->assertSerializesAndUnserializedCorrectly($structure);
        }
    }
    
    private function recompile(Parsing\IFunctionReflection $reflection, Parsing\IFunctionStructure $structure, &$closure = null)
    {
        $signature = $reflection->getSignature();
        
        $closure = $closureExpression = O\Expression::closure(
                $signature->returnsReference(), 
                $reflection->getInnerReflection()->getClosureScopeClass() === null, 
                $signature->getParameterExpressions(), 
                $signature->getScopedVariableNames() ?: [],
                $structure->getBodyExpressions());
        
        $__compiledCode__       = $closureExpression->compile();
        $__scopedVariables__    = $reflection->getScope()->getVariableValueMap();
        
        unset($closureExpression, $reflection, $reflection, $structure);
        
        extract($__scopedVariables__);
        return eval("return $__compiledCode__;");
    }

    private function assertSerializesAndUnserializedCorrectly(Parsing\IFunctionStructure $function)
    {
        $serializedFunction = unserialize(serialize($function));

        $this->assertEquals($function, $serializedFunction);
    }

    final protected function assertScopedVariables(callable $function, array $variableValueMap, $removeThis = true)
    {
        $this->verifyImplementation();
        

        $reflection = $this->currentImplementation->getReflection($function);
        $scopedVariables = $reflection->getScope()->getVariableValueMap();
        
        if($removeThis) {
            unset($scopedVariables['this'], $variableValueMap['this']);
        }

        $this->assertSame(
                $scopedVariables,
                $variableValueMap);
    }

    final protected function assertParametersAre(callable $function, array $parameterExpresssions)
    {
        $this->verifyImplementation();

        $reflection = $this->currentImplementation->getReflection($function);

        $this->assertEquals(
                $reflection->getSignature()->getParameterExpressions(),
                $parameterExpresssions);
    }
}
