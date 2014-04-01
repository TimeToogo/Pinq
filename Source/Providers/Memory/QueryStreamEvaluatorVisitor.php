<?php

namespace Pinq\Providers\Memory;

use \Pinq\Queries;
use \Pinq\Queries\Functional;

abstract class QueryStreamEvaluatorVisitor extends Functional\QueryStreamVisitor implements IQueryStreamEvaluator
{
    protected $Values = [];

    final public function Evaluate(Queries\IQueryStream $QueryStream)
    {
        $this->Visit($QueryStream);
    }

    final public function SetValues(array $Values)
    {
        $this->Values = $Values;
    }

    public function GetValues()
    {
        return $this->Values;
    }

    final protected function MultisortPreserveKeys(Functional\OrderBy $Query, array &$Values)
    {
        $this->MakeKeysString($Values);
        $Arguments = $this->GetMultisortArguments($Query, $Values);
        call_user_func_array('array_multisort', $Arguments);
        $this->UnserializeKeysString($Values);
    }

    private function GetMultisortArguments(Functional\OrderBy $Query, array &$Values)
    {
        $OrderFunctions = $Query->GetFunctions();
        $IsAcendingArray = $Query->GetIsAscendingArray();

        $MultiSortArguments = [];
        foreach ($OrderFunctions as $Key => $OrderFunction) {
            $OrderColumnValues = array_map($OrderFunction, $Values);

            $MultiSortArguments[] =& $OrderColumnValues;
            $MultiSortArguments[] = $IsAcendingArray[$Key] ? SORT_ASC : SORT_DESC;
            $MultiSortArguments[] = SORT_REGULAR;
        }

        $MultiSortArguments[] =& $Values;

        return $MultiSortArguments;
    }

    private function MakeKeysString(array &$Array)
    {
        $NewArray = [];
        foreach ($Array as $Key => &$Value) {
            $NewArray['a' . $Key] =& $Value;
        }

        $Array = $NewArray;
    }

    private function UnserializeKeysString(array &$Array)
    {
        $NewArray = [];
        foreach ($Array as $Key => &$Value) {
            $NewArray[substr($Key, 1)] =& $Value;
        }

        $Array = $NewArray;
    }
}
