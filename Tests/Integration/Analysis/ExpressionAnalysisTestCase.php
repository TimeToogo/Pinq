<?php

namespace Pinq\Tests\Integration\Analysis;

use Pinq\Analysis\AnalysisContext;
use Pinq\Analysis\ExpressionAnalyser;
use Pinq\Analysis\IExpressionAnalyser;
use Pinq\Analysis\IType;
use Pinq\Analysis\ITypeAnalysis;
use Pinq\Analysis\ITypeSystem;
use Pinq\Analysis\PhpTypeSystem;
use Pinq\Expressions\EvaluationContext;
use Pinq\Expressions as O;
use Pinq\Parsing\FunctionInterpreter;
use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Tests\PinqTestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionAnalysisTestCase extends PinqTestCase
{
    /**
     * @var IFunctionInterpreter
     */
    protected $functionInterpreter;

    /**
     * @var ITypeSystem
     */
    protected $typeSystem;

    /**
     * @var IExpressionAnalyser
     */
    protected $expressionAnalyser;

    protected function setUp(): void
    {
        $this->functionInterpreter = $this->functionInterpreter();
        $this->typeSystem          = $this->setUpTypeSystem();
        $this->expressionAnalyser  = $this->setUpExpressionAnalyser();
    }

    /**
     * @return IFunctionInterpreter
     */
    protected function functionInterpreter()
    {
        return FunctionInterpreter::getDefault();
    }

    /**
     * @return ITypeSystem
     */
    protected function setUpTypeSystem()
    {
        return new PhpTypeSystem();
    }

    /**
     * @return ITypeSystem
     */
    protected function setUpExpressionAnalyser()
    {
        return new ExpressionAnalyser($this->typeSystem);
    }

    protected function assertReturnsType(callable $expression, IType $expected, array $variableTypeMap = [])
    {
        $analysis = $this->getAnalysis($expression, $variableTypeMap);
        $compiled = $analysis->getExpression()->compileDebug();
        $returnedType = $analysis->getReturnedType();

        $this->assertEqualTypes($expected, $returnedType, $compiled);
    }

    protected function assertEqualTypes(IType $expected, IType $actual, $message = '')
    {
        $this->assertSame($expected->getIdentifier(), $actual->getIdentifier(), $message);
        $this->assertTrue($expected->isEqualTo($actual), $message);
        $this->assertTrue($actual->isEqualTo($expected), $message);
        $this->assertSame($expected, $actual, $message);
    }

    protected function assertEqualsNativeType($nativeType, IType $actual, $message = '')
    {
        $this->assertEqualTypes($this->typeSystem->getNativeType($nativeType), $actual, $message);
    }

    protected function assertEqualsObjectType($classType, IType $actual, $message = '')
    {
        $this->assertEqualTypes($this->typeSystem->getObjectType($classType), $actual, $message);
    }

    protected function assertReturnsNativeType(callable $expression, $nativeType, array $variableTypeMap = [])
    {
        $this->assertReturnsType($expression, $this->typeSystem->getNativeType($nativeType), $variableTypeMap);
    }

    protected function assertReturnsObjectType(callable $expression, $objectType, array $variableTypeMap = [])
    {
        $this->assertReturnsType($expression, $this->typeSystem->getObjectType($objectType), $variableTypeMap);
    }

    protected function assertAnalysisFails(callable $expression, array $variableTypeMap = [], $message = '')
    {
        $failed = false;
        try {
            $this->getAnalysis($expression, $variableTypeMap);
            $failed = true;
        } catch (\Exception $exception) {}

        if($failed) {
            $this->fail(
                    'Expecting analysis to fail with exception of type \\Pinq\\Analysis\\TypeException: no exception was thrown' . $message
            );
        }
    }

    /**
     * @param callable $function
     * @param array    $variableTypeMap
     * @param mixed    $expression
     *
     * @return ITypeAnalysis
     */
    protected function getAnalysis(callable $function, array $variableTypeMap = [], &$expression = null)
    {
        $reflection = $this->functionInterpreter->getReflection($function);
        foreach ($reflection->getSignature()->getParameterExpressions() as $parameterExpression) {
            $variableTypeMap[$parameterExpression->getName()] = $this->typeSystem->getTypeFromTypeHint(
                    $parameterExpression->getTypeHint()
            );
        }
        $analysisContext = $this->expressionAnalyser->createAnalysisContext($reflection->asEvaluationContext());
        foreach ($variableTypeMap as $variable => $type) {
            $analysisContext->setExpressionType(O\Expression::variable(O\Expression::value($variable)), $type);
        }

        $bodyExpressions = $this->functionInterpreter->getStructure($reflection)->getBodyExpressions();
        foreach($bodyExpressions as $expression) {
            if($expression instanceof O\ReturnExpression) {
                return $this->expressionAnalyser->analyse($analysisContext, $expression->getValue());
            } elseif( count($bodyExpressions) === 1) {
                return $this->expressionAnalyser->analyse($analysisContext, $expression);
            } else {
                $this->expressionAnalyser->analyse($analysisContext, $expression);
            }
        }
    }
} 