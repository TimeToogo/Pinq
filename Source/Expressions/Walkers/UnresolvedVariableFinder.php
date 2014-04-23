<?php

namespace Pinq\Expressions\Walkers;

use Pinq\Expressions as O;

/**
 * Locates and stores all unresolved variables
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class UnresolvedVariableFinder extends O\ExpressionWalker
{
    /**
     * @var string[]
     */
    private $variablesToIgnore = [];

    /**
     * @var string[]
     */
    private $unresolvedVariables = [];

    /**
     * @return void
     */
    public function resetUnresolvedVariables()
    {
        $this->unresolvedVariables = [];
    }

    /**
     * The unresolved variables
     *
     * @return string[]
     */
    public function getUnresolvedVariables()
    {
        return $this->unresolvedVariables;
    }

    public function walkAssignment(O\AssignmentExpression $expression)
    {
        $assignToExpression = $expression->getAssignToExpression();

        //Ignore variable if making assignment
        if ($assignToExpression instanceof O\VariableExpression && in_array($expression->getOperator(), [O\Operators\Assignment::EQUAL, O\Operators\Assignment::EQUAL_REFERENCE])) {
            $this->walk($assignToExpression->getNameExpression());
        } else {
            $this->walk($assignToExpression);
        }

        $assignmentValueExpression = $expression->getAssignmentValueExpression();
        $this->walk($assignmentValueExpression);

        return $expression;
    }

    public function walkClosure(O\ClosureExpression $expression)
    {
        foreach ($expression->getUsedVariableNames() as $usedVariableName) {
            $this->walkVariable(O\Expression::variable(O\Expression::value($usedVariableName)));
        }

        $originalVariablesToIgnore = $this->variablesToIgnore;
        $this->variablesToIgnore = array_map(function ($i) {
            return $i->getName();
        }, $expression->getParameterExpressions());
        $this->walkAll($expression->getBodyExpressions());
        $this->variablesToIgnore = $originalVariablesToIgnore;

        return $expression;
    }

    public function walkVariable(O\VariableExpression $expression)
    {
        $nameExpression = $expression->getNameExpression();
        $variableName = $nameExpression instanceof O\ValueExpression ? $nameExpression->getValue() : $nameExpression->compile();
        $this->addUnresolvedVariable($variableName);

        return $expression;
    }

    private function addUnresolvedVariable($variableName)
    {
        if (!in_array($variableName, $this->unresolvedVariables) && !in_array($variableName, $this->variablesToIgnore)) {
            $this->unresolvedVariables[] = $variableName;
        }
    }
}
