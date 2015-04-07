<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Implementation of the expression type analyser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionAnalyser extends O\ExpressionVisitor implements IExpressionAnalyser
{
    /**
     * @var ITypeSystem
     */
    protected $typeSystem;

    /**
     * @var IAnalysisContext
     */
    protected $analysisContext;

    /**
     * @var \SplObjectStorage|IType[]
     */
    protected $analysis;

    /**
     * @var \SplObjectStorage
     */
    protected $metadata;

    public function __construct(ITypeSystem $typeSystem)
    {
        $this->typeSystem = $typeSystem;
    }

    public function getTypeSystem()
    {
        return $this->typeSystem;
    }

    public function createAnalysisContext(O\IEvaluationContext $evaluationContext)
    {
        return new AnalysisContext($this->typeSystem, $evaluationContext);
    }

    public function analyse(IAnalysisContext $analysisContext, O\Expression $expression)
    {
        $this->analysisContext = $analysisContext;
        $this->analysis        = new \SplObjectStorage();
        $this->metadata        = new \SplObjectStorage();

        $this->walk($expression);

        return new TypeAnalysis($this->typeSystem, $expression, $this->analysis, $this->metadata);
    }

    public function visitArray(O\ArrayExpression $expression)
    {
        $this->walkAll($expression->getItems());
        $this->analysis[$expression] = $this->typeSystem->getNativeType(INativeType::TYPE_ARRAY);
    }

    public function visitArrayItem(O\ArrayItemExpression $expression)
    {
        $this->walk($expression->getKey());
        $this->walk($expression->getValue());
    }

    public function visitAssignment(O\AssignmentExpression $expression)
    {
        $assignTo = $expression->getAssignTo();
        $assignmentValue = $expression->getAssignmentValue();

        $this->walk($assignmentValue);

        $operator = $expression->getOperator();
        if ($operator === O\Operators\Assignment::EQUAL) {
            $this->analysisContext->setExpressionType($assignTo, $this->analysis[$assignmentValue]);
            $this->analysis[$expression] = $this->analysis[$assignmentValue];
        } elseif ($operator === O\Operators\Assignment::EQUAL_REFERENCE) {
            $this->analysisContext->removeExpressionType($assignTo);
            $this->analysisContext->setExpressionType($assignTo, $this->analysis[$assignmentValue]);
            $this->analysisContext->createReference($assignTo, $assignmentValue);
            $this->analysis[$expression] = $this->analysis[$assignmentValue];
        } else {
            $this->walk($assignTo);
            $binaryOperation             = $this->typeSystem->getBinaryOperation(
                    $this->analysis[$assignTo],
                    O\Operators\Assignment::toBinaryOperator($operator),
                    $this->analysis[$assignmentValue]
            );
            $this->analysis[$expression] = $binaryOperation->getReturnType();
        }
    }

    public function visitBinaryOperation(O\BinaryOperationExpression $expression)
    {
        $this->walk($expression->getLeftOperand());
        $this->walk($expression->getRightOperand());

        $binaryOperation             = $this->typeSystem->getBinaryOperation(
                $this->analysis[$expression->getLeftOperand()],
                $expression->getOperator(),
                $this->analysis[$expression->getRightOperand()]
        );
        $this->metadata[$expression] = $binaryOperation;
        $this->analysis[$expression] = $binaryOperation->getReturnType();
    }

    protected function addTypeOperation(O\Expression $expression, ITypeOperation $typeOperation)
    {
        $this->metadata[$expression] = $typeOperation;
        $this->analysis[$expression] = $typeOperation->getReturnType();
    }

    public function visitUnaryOperation(O\UnaryOperationExpression $expression)
    {
        $this->walk($expression->getOperand());
        $this->addTypeOperation(
                $expression,
                $this->analysis[$expression->getOperand()]->getUnaryOperation($expression)
        );
    }

    public function visitCast(O\CastExpression $expression)
    {
        $this->walk($expression->getCastValue());
        $this->addTypeOperation(
                $expression,
                $this->analysis[$expression->getCastValue()]->getCast($expression)
        );
    }

    public function visitConstant(O\ConstantExpression $expression)
    {
        $this->verifyConstantDefined($expression->getName());

        $this->analysis[$expression] = $this->typeSystem->getTypeFromValue($expression->evaluate($this->analysisContext->getEvaluationContext()));
    }

    public function visitClassConstant(O\ClassConstantExpression $expression)
    {
        $this->validateStaticClassName($expression->getClass(), 'class constant');
        $this->verifyConstantDefined($expression->getClass()->getValue() . '::' . $expression->getName());

        $this->analysis[$expression] = $this->typeSystem->getTypeFromValue($expression->evaluate($this->analysisContext->getEvaluationContext()));
    }

    private function verifyConstantDefined($constantName)
    {
        if (!defined($constantName)) {
            throw new TypeException('Cannot get type from constant %s: constant is not defined', $constantName);
        }
    }

    public function visitEmpty(O\EmptyExpression $expression)
    {
        $this->walk($expression->getValue());
        $this->analysis[$expression] = $this->typeSystem->getNativeType(INativeType::TYPE_BOOL);
    }

    public function visitIsset(O\IssetExpression $expression)
    {
        $this->walkAll($expression->getValues());
        $this->analysis[$expression] = $this->typeSystem->getNativeType(INativeType::TYPE_BOOL);
    }

    public function visitUnset(O\UnsetExpression $expression)
    {
        $this->walkAll($expression->getValues());
        $this->analysis[$expression] = $this->typeSystem->getType(INativeType::TYPE_NULL);
    }

    public function visitField(O\FieldExpression $expression)
    {
        $this->walk($expression->getValue());
        $this->walk($expression->getName());

        $this->addTypeOperation(
                $expression,
                $this->analysis[$expression->getValue()]->getField($expression)
        );
    }

    public function visitMethodCall(O\MethodCallExpression $expression)
    {
        $this->walk($expression->getValue());
        $this->walk($expression->getName());
        $this->walkAll($expression->getArguments());

        $this->addTypeOperation(
                $expression,
                $this->analysis[$expression->getValue()]->getMethod($expression)
        );
    }

    public function visitIndex(O\IndexExpression $expression)
    {
        $this->walk($expression->getValue());
        $this->walk($expression->getIndex());

        $this->addTypeOperation(
                $expression,
                $this->analysis[$expression->getValue()]->getIndex($expression)
        );
    }

    public function visitInvocation(O\InvocationExpression $expression)
    {
        $this->walk($expression->getValue());
        $this->walkAll($expression->getArguments());

        $this->addTypeOperation(
                $expression,
                $this->analysis[$expression->getValue()]->getInvocation($expression)
        );
    }

    public function visitFunctionCall(O\FunctionCallExpression $expression)
    {
        $nameExpression = $expression->getName();
        $this->walk($nameExpression);
        $this->walkAll($expression->getArguments());

        if ($nameExpression instanceof O\ValueExpression) {
            $function                    = $this->typeSystem->getFunction($nameExpression->getValue());
            $this->metadata[$expression] = $function;
            $this->analysis[$expression] = $function->getReturnType();
        } else {
            throw new TypeException('Invalid function expression: dynamic function calls are not allowed');
        }
    }

    protected function validateStaticClassName(O\Expression $expression, $type)
    {
        if ($expression instanceof O\ValueExpression) {
            return $expression->getValue();
        } else {
            throw new TypeException('Invalid %s expression: dynamic class types are not supported', $type);
        }
    }

    public function visitStaticMethodCall(O\StaticMethodCallExpression $expression)
    {
        $classExpression = $expression->getClass();
        $this->walk($classExpression);
        $this->walk($expression->getName());
        $this->walkAll($expression->getArguments());

        $class = $this->validateStaticClassName($classExpression, 'static method call');
        $this->addTypeOperation(
                $expression,
                $this->typeSystem->getObjectType($class)->getStaticMethod($expression)
        );
    }

    public function visitStaticField(O\StaticFieldExpression $expression)
    {
        $classExpression = $expression->getClass();
        $this->walk($classExpression);
        $this->walk($expression->getName());

        $class = $this->validateStaticClassName($classExpression, 'static field');

        $this->addTypeOperation(
                $expression,
                $this->typeSystem->getObjectType($class)->getStaticField($expression)
        );
    }

    public function visitNew(O\NewExpression $expression)
    {
        $classExpression = $expression->getClass();
        $this->walk($classExpression);
        $this->walkAll($expression->getArguments());

        $class = $this->validateStaticClassName($classExpression, 'new');
        $this->addTypeOperation(
                $expression,
                $this->typeSystem->getObjectType($class)->getConstructor($expression)
        );
    }

    public function visitTernary(O\TernaryExpression $expression)
    {
        $this->walk($expression->getCondition());
        $this->walk($expression->getIfTrue());
        $this->walk($expression->getIfFalse());

        $this->analysis[$expression] = $this->typeSystem->getCommonAncestorType(
                $this->analysis[$expression->hasIfTrue() ? $expression->getIfTrue() : $expression->getCondition()],
                $this->analysis[$expression->getIfFalse()]
        );
    }

    public function visitVariable(O\VariableExpression $expression)
    {
        $nameExpression = $expression->getName();
        $this->walk($nameExpression);

        $type = $this->analysisContext->getExpressionType($expression);
        if ($type === null) {
            throw new TypeException(
                    'Invalid variable expression: \'%s\' type is unknown',
                    $nameExpression->compileDebug());
        }

        $this->analysis[$expression] = $type;
    }

    public function visitValue(O\ValueExpression $expression)
    {
        $this->analysis[$expression] = $this->typeSystem->getTypeFromValue($expression->getValue());
    }

    public function visitClosure(O\ClosureExpression $expression)
    {
        $originalContext = $this->analysisContext;
        $this->analysisContext   = $originalContext->inNewScope();

        foreach ($expression->getParameters() as $parameter) {
            $this->walk($parameter);
            $typeHintType = $this->typeSystem->getTypeFromTypeHint($parameter->getTypeHint());
            if (!$parameter->hasDefaultValue()
                    || $this->analysis[$parameter->getDefaultValue()]->isEqualTo($typeHintType)
            ) {
                $this->analysisContext->setExpressionType($parameter->asVariable(), $typeHintType);
            } else {
                $this->analysisContext->setExpressionType(
                        $parameter->asVariable(),
                        $this->typeSystem->getNativeType(INativeType::TYPE_MIXED)
                );
            }
        }

        foreach ($expression->getUsedVariables() as $usedVariable) {
            $variable = $usedVariable->asVariable();
            //TODO: handle references with used variables. Probably impossible though.
            $this->analysisContext->setExpressionType($variable, $originalContext->getExpressionType($variable));
        }

        $this->walkAll($expression->getBodyExpressions());
        $this->analysis[$expression] = $this->typeSystem->getObjectType('Closure');
        $this->analysisContext       = $originalContext;
    }

    public function visitReturn(O\ReturnExpression $expression)
    {
        $this->walk($expression->getValue());
    }

    public function visitThrow(O\ThrowExpression $expression)
    {
        $this->walk($expression->getException());
    }

    public function visitParameter(O\ParameterExpression $expression)
    {
        $this->walk($expression->getDefaultValue());
    }

    public function visitArgument(O\ArgumentExpression $expression)
    {
        $this->walk($expression->getValue());
    }
}
