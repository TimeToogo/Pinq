<?php

namespace Pinq\Providers\Arrays;

use \Pinq\Queries;
use \Pinq\Queries\Functional;
use \Pinq\Providers\Memory;

class QueryStreamEvaluatorVisitor extends Memory\QueryStreamEvaluatorVisitor
{
    public function VisitFilter(Functional\Filter $Query)
    {
        $this->Values = array_filter($this->Values, $Query->GetFunction());
    }

    public function VisitOrderBy(Functional\OrderBy $Query)
    {
        $this->MultisortPreserveKeys($Query, $this->Values);
    }

    public function VisitRange(Queries\Range $Query)
    {
        array_splice($this->Values, $Query->GetRangeStart(), $Query->GetRangeAmount());
    }

    public function VisitGroupBy(Functional\GroupBy $Query)
    {
        $GroupByFunction = $Query->GetFunction();

        $GroupKeyMap = array_combine(array_keys($this->Values), array_map($GroupByFunction, $this->Values));

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

            $Groups[$GroupArrayKey][] = $this->Values[$ValueKey];
        }

        $this->Values = array_map(function ($Group) { return new \Pinq\Traversable($Group); }, $Groups);
    }

    public function VisitUnique(Queries\Unique $Query)
    {
        $SeenKeys = [];
        $SeenValues = [];
        foreach ($this->Values as $Key => $Value) {
            if (is_int($Value) || is_string($Value)) {
                if (!isset($SeenKeys[$Value])) {
                    $SeenKeys[$Value] = true;
                    continue;
                }
            } 
            else if (!in_array($Value, $SeenValues, true)) {
                $SeenValues[] = $Value;
                continue;
            }

            unset($this->Values[$Key]);
        }
    }

    public function VisitSelect(Functional\Select $Query)
    {
        $this->Values = array_map($Query->GetFunction(), $this->Values);
    }

    public function VisitSelectMany(Functional\SelectMany $Query)
    {
        $ArrayValues = array_map($Query->GetFunction(), $this->Values);

        foreach ($ArrayValues as $Key => $Value) {
            if (is_array($Value)) {
                continue;
            } 
            else if ($Value instanceof \Traversable) {
                $ArrayValues[$Key] = iterator_to_array($Value);
            } 
            else {
                $ArrayValues[$Key] = [$Value];
            }
        }

        $this->Values = call_user_func_array('array_merge', $ArrayValues);
    }
    
    public function VisitIndexBy(Functional\IndexBy $Query)
    {
        $this->Values = array_combine(array_map($Query->GetFunction(), $this->Values), $this->Values);
    }

    public function VisitOperation(Queries\Operation $Query)
    {
        $Traversable = $Query->GetTraversable();
        $OtherValues = $Traversable->AsArray();

        switch ($Query->GetOperationType()) {
            case Queries\Operation::Union:
                foreach ($OtherValues as $OtherValue) {
                    if (!in_array($OtherValue, $this->Values, true)) {
                        $this->Values[] = $OtherValues;
                    }
                }
                break;

            case Queries\Operation::Append:
                $this->Values = array_merge($this->Values, $OtherValues);
                break;

            case Queries\Operation::Intersect:
                $this->Values = array_uintersect($this->Values, $OtherValues, \Pinq\Utilities::$Identical);
                break;

            case Queries\Operation::Except:
                $this->Values = array_udiff($this->Values, $OtherValues, \Pinq\Utilities::$Identical);
                break;
        }
    }
}
