<?php

namespace Pinq\Providers\Generators;

use \Pinq\Queries;
use \Pinq\Queries\Functional;
use \Pinq\Providers\Memory;

/**
 * Experimental
 */
class QueryStreamEvaluatorVisitor extends Memory\QueryStreamEvaluatorVisitor
{
    public function GetValues()
    {
        return $this->ValuesAsArray($this->Values);
    }

    private function ValuesAsArray($Values)
    {
        return is_array($Values) ? $Values : iterator_to_array($Values);
    }

    public function VisitFilter(Functional\Filter $Query)
    {
        $this->Values = $this->Filter($this->Values, $Query);
    }
    public function Filter($Values, Functional\FunctionQuery $Query)
    {
        $Function = $Query->GetFunction();
        foreach ($Values as $Key => $Value) {
            if ($Function($Value)) {
                yield $Key => $Value;
            }
        }
    }

    public function VisitOrderBy(Functional\OrderBy $Query)
    {
        $this->Values = $this->OrderBy($this->Values, $Query);
    }

    public function OrderBy($Values, Functional\OrderBy $Query)
    {
        $Values = $this->ValuesAsArray($Values);
        $this->MultisortPreserveKeys($Query, $Values);

        return $Values;
    }

    public function VisitRange(Queries\Range $Query)
    {
        $this->Values = $this->Range($this->Values, $Query);
    }
    public function Range($Values, Queries\Range $Query)
    {
        $Count = 0;
        $RangeStart = $Query->GetRangeStart();
        $RangeAmount = $Query->GetRangeAmount();

        foreach ($Values as $Key => $Value) {
            if ($Count >= $RangeStart) {
                yield $Key => $Value;
            }
            if ($Count - $RangeStart === $RangeAmount) {
                break;
            }

            $Count++;
        }
    }

    public function VisitGroupBy(Functional\GroupBy $Query)
    {
        $this->Values = $this->GroupBy($this->Values, $Query);
    }
    public function GroupBy($Values, Functional\GroupBy $Query)
    {
        $GroupByFunction = $Query->GetFunction();

        $Values = $this->ValuesAsArray($Values);
        $GroupKeyMap = array_combine(array_keys($Values), array_map($GroupByFunction, $Values));

        $Groups = [];

        $SeenKeys = [];
        $SeenValues = [];
        foreach ($GroupKeyMap as $ValueKey => $GroupKey) {
            $GroupArrayKey = $ValueKey;

            if (is_int($GroupKey) || is_string($GroupKey)) {
                if (!isset($SeenKeys[$GroupKey])) {
                    $SeenKeys[$GroupKey] = $GroupArrayKey;
                    $Groups[$GroupArrayKey] = [];
                }

                $GroupArrayKey = $SeenKeys[$GroupKey];
            } 
            else {
                $SeenAggregateKey = array_search($GroupKey, $SeenValues, true);

                if ($SeenAggregateKey === false) {
                    $SeenValues[$GroupArrayKey] = $GroupKey;
                    $Groups[$GroupArrayKey] = [];
                }
            }

            $Groups[$GroupArrayKey][] = $Values[$ValueKey];
        }

        foreach (array_map(function ($Group) { return new \Pinq\Traversable($Group); }, $Groups) as $Key => $Group) {
            yield $Key => $Group;
        }
    }

    public function VisitUnique(Queries\Unique $Query)
    {
        $this->Values = $this->Unique($this->Values);
    }
    public function Unique($Values)
    {
        $SeenKeys = [];
        $SeenValues = [];

        foreach ($Values as $Key => $Value) {
            if (is_int($Value) || is_string($Value)) {
                if (!isset($SeenKeys[$Value])) {
                    yield $Key => $Value;
                }
            } 
            else if (!in_array($Value, $SeenValues, true)) {
                $SeenValues[] = $Value;
                yield $Key => $Value;
            }
        }
    }

    public function VisitSelect(Functional\Select $Query)
    {
        $this->Values = $this->Select($this->Values, $Query);
    }
    public function Select($Values, Functional\FunctionQuery $Query)
    {
        $Function = $Query->GetFunction();

        foreach ($Values as $Key => $Value) {
            yield $Key => $Function($Value);
        }
    }

    public function VisitSelectMany(Functional\SelectMany $Query)
    {
        $this->Values = $this->SelectMany($this->Values, $Query);
    }
    public function SelectMany($Values, Functional\SelectMany $Query)
    {
        $Function = $Query->GetFunction();
        foreach ($Values as $ManyValue) {
            foreach ($Function($ManyValue) as $Value) {
                yield $Value;
            }
        }
    }
    
    public function VisitIndexBy(Functional\IndexBy $Query)
    {
        $this->Values = $this->IndexBy($this->Values, $Query);
    }
    public function IndexBy($Values, Functional\IndexBy $Query)
    {
        $Fucntion = $Query->GetFunction();
        foreach ($Values as $Value) {
            yield $Fucntion($Value) => $Value;
        }
    }

    public function VisitOperation(Queries\Operation $Query)
    {
        $Traversable = $Query->GetTraversable();

        switch ($Query->GetOperationType()) {
            case Queries\Operation::Union:
                $this->Values = $this->Union($this->Values, $Traversable);
                break;

            case Queries\Operation::Append:
                $this->Values = $this->Append($this->Values, $Traversable);
                break;

            case Queries\Operation::Intersect:
                $this->Values = $this->Intersect($this->Values, $Traversable);
                break;

            case Queries\Operation::Except:
                $this->Values = $this->Except($this->Values, $Traversable);
                break;
        }
    }

    private function Union($Values, \Pinq\ITraversable $OtherValues)
    {
        $OtherValues = $OtherValues->AsArray();
        foreach ($Values as $Value) {
            yield $Value;
        }
        foreach ($OtherValues as $OtherValue) {
            if (!in_array($OtherValue, $Values, true)) {
                yield $OtherValue;
            }
        }
    }

    private function Append($Values, \Pinq\ITraversable $OtherValues)
    {
        foreach ($Values as $Value) {
            yield $Value;
        }
        foreach ($OtherValues as $Value) {
            yield $Value;
        }
    }

    private function Intersect($Values, \Pinq\ITraversable $OtherValues)
    {
        $OtherValues = $OtherValues->AsArray();
        foreach ($Values as $Value) {
            if (in_array($Value, $OtherValues, true)) {
                yield $Value;
            }
        }
    }

    private function Except($Values, \Pinq\ITraversable $OtherValues)
    {
        $OtherValues = $OtherValues->AsArray();
        foreach ($Values as $Value) {
            if (!in_array($Value, $OtherValues, true)) {
                yield $Value;
            }
        }
    }
}
