<?php

namespace Pinq\Expressions;

class ClosureExpression extends Expression
{
    private $ParameterNameTypeHintMap;
    private $UsedVariables;
    private $BodyExpressions;

    public function __construct(array $ParameterNameTypeHintMap, array $UsedVariables, array $BodyExpressions)
    {
        $this->ParameterNameTypeHintMap = $ParameterNameTypeHintMap;
        $this->UsedVariables = $UsedVariables;
        $this->BodyExpressions = $BodyExpressions;
    }

    public function GetParameterNameTypeHintMap()
    {
        return $this->ParameterNameTypeHintMap;
    }

    public function GetUsedVariableNames()
    {
        return $this->UsedVariables;
    }

    /**
     * @return Expression[]
     */
    public function GetBodyExpressions()
    {
        return $this->BodyExpressions;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkClosure($this);
    }

    public function Simplify()
    {
        return $this->Update(
                $this->ParameterNameTypeHintMap,
                $this->UsedVariables,
                self::SimplifyAll($this->BodyExpressions));
    }

    public function Update(array $ParameterNameTypeHintMap, array $UsedVariables, array $BodyExpressions)
    {
        if ($this->ParameterNameTypeHintMap === $ParameterNameTypeHintMap
                && $this->UsedVariables === $UsedVariables
                && $this->BodyExpressions === $BodyExpressions) {
            return $this;
        }

        return new self($ParameterNameTypeHintMap, $UsedVariables, $BodyExpressions);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= 'function (';
        if (!empty($this->ParameterNameTypeHintMap)) {
            $Parameters = [];
            foreach ($this->ParameterNameTypeHintMap as $Name => $TypeHint) {
                $Parameters[] = $TypeHint . ' $' . $Name;
            }
            $Code .= implode(',', $Parameters);
        }
        $Code .= ')';
        if (!empty($this->UsedVariables)) {
            $Code .= 'use (';
            $Code .= '$' . implode(', $', $this->UsedVariables);
            $Code .= ')';
        }
        $Code .= '{';
        foreach ($this->BodyExpressions as $Expression) {
            $Expression->CompileCode($Code);
            $Code .= ';';
        }
        $Code .= '}';
    }
}
