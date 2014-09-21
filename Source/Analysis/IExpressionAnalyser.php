<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Interface of a expression type analyser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IExpressionAnalyser
{
    /**
     * Gets the type system for the expression analyser.
     *
     * @return ITypeSystem
     */
    public function getTypeSystem();

    /**
     * Creates a new analysis context with the supplied evaluation context.
     *
     * @param O\IEvaluationContext $evaluationContext
     *
     * @return IAnalysisContext
     */
    public function createAnalysisContext(O\IEvaluationContext $evaluationContext);

    /**
     * Analyses the supplied expression tree.
     *
     * @param IAnalysisContext $analysisContext
     * @param O\Expression     $expression
     *
     * @return ITypeAnalysis
     */
    public function analyse(IAnalysisContext $analysisContext, O\Expression $expression);
}
