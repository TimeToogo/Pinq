<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Implementation of the analysis context.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AnalysisContext extends Typed implements IAnalysisContext
{
    /**
     * @var O\IEvaluationContext
     */
    protected $evaluationContext;

    /**
     * @var IType[]
     */
    protected $expressionTypes = [];

    public function __construct(ITypeSystem $typeSystem, O\IEvaluationContext $evaluationContext)
    {
        parent::__construct($typeSystem);
        $this->evaluationContext = $evaluationContext;
        foreach ($evaluationContext->getVariableTable() as $variable => $value) {
            $this->setExpressionType(O\Expression::variable(O\Expression::value($variable)), $typeSystem->getTypeFromValue($value));
        }
    }

    public function getEvaluationContext()
    {
        return $this->evaluationContext;
    }

    public function getExpressionType(O\Expression $expression)
    {
        $hash = $expression->hash();

        return isset($this->expressionTypes[$hash]) ? $this->expressionTypes[$hash] : null;
    }

    public function setExpressionType(O\Expression $expression, IType $type)
    {
        $this->expressionTypes[$expression->hash()] = $type;
    }

    public function removeExpressionType(O\Expression $expression)
    {
        unset($this->expressionTypes[$expression->hash()]);
    }

    public function createReference(O\Expression $expression, O\Expression $referencedExpression)
    {
        $this->expressionTypes[$expression->hash()] =& $this->expressionTypes[$referencedExpression->hash()];
    }

    public function inNewScope()
    {
        return new self($this->typeSystem, $this->evaluationContext);
    }
}
