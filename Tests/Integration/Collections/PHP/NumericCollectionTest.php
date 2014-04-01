<?php

namespace Pinq\Tests\Integration\Collections\PHP;

class NumericCollectionTest extends MemoryCollectionTest
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function ArrayData()
    {
        return range(1, 1000);
    }

    public function testOrderByTensThenDescending()
    {
        $Collection = $this->Collection
                ->OrderBy(function ($I) { return (int) ($I / 10); })
                ->ThenByDescending(function ($I) { return $I; });

        $EquivalentArray = [];
        $Array = [];
        foreach ($this->ArrayData as $Key => $Value) {
            if ($Value % 10 === 0) {
                $EquivalentArray += array_reverse($Array, true);
                $Array = [];
            }
            $Array[$Key] = $Value;
        }
        $EquivalentArray += array_reverse($Array, true);

        $this->AssertMatches($Collection, $EquivalentArray);
    }

    public function testSimpleAggregation()
    {
        $this->assertEquals(array_sum($this->ArrayData), $this->Collection->Sum());
    }

    public function testComplexAggregationQuery()
    {
        $Collection = $this->Collection
                ->Where(function ($I) { return $I % 2 === 0; })
                ->OrderBy(function ($I) { return -$I; })
                ->GroupBy(function ($I) { return $I % 7; })
                ->Where(function (\Pinq\IQueryable $I) { return $I->Count() % 2 === 0; })
                ->Select(function (\Pinq\IQueryable $Numbers) {
                    return [
                        'First' => $Numbers->First(),
                        'Average' => $Numbers->Average(),
                        'Count' => $Numbers->Count(),
                        'Numbers' => $Numbers->AsArray(),
                    ];
                })
                ->IndexBy(function (array $Values) { return implode(',', $Values['Numbers']); });

        $NewData = array_filter($this->ArrayData, function ($I) { return $I % 2 === 0; });
        $NewData = array_reverse($NewData, true);
        $Aggregates = [];
        foreach ($NewData as $Key => $Value) {
            $AggregateKey = array_search($Value % 7, array_map(function ($I) { return $I['Key']; }, $Aggregates));
            if ($AggregateKey === false) {
                $AggregateKey = count($Aggregates) + 1;
                $Aggregates[$AggregateKey] = [
                    'Key' => $Value % 7,
                    'First' => $Value,
                    'Numbers' => [],
                    'Count' => 0,
                ];
            }

            $Aggregates[$AggregateKey]['Numbers'][] = $Value;
            $Aggregates[$AggregateKey]['Count']++;
        }
        $IndexedAggregates = [];
        foreach ($Aggregates as $Key => &$Value) {
            if ($Value['Count'] % 2 !== 0) {
                continue;
            }
            unset($Value['Key']);
            $Value['Average'] = array_sum($Value['Numbers']) / $Value['Count'];
            $IndexedAggregates[implode(',', $Value['Numbers'])] =& $Value;
        }

        $this->AssertMatches($Collection, $IndexedAggregates, 'Complex Aggregate');
    }

    public function testUpdateValuesFunction()
    {
        $Collection = $this->Collection
                ->Where(function ($I) { return $I % 2 === 0; });

        $Collection->Apply(function (&$I) { $I *= 10; });

        $NewData = array_filter($this->ArrayData, function ($I) { return $I % 2 === 0; });
        $NewData = array_map(function ($I) { return $I * 10; }, $NewData);

        $this->AssertMatches($Collection, $NewData, 'Update');
    }

    public function testRemovalValuesFunction()
    {
        $this->Collection
                ->RemoveWhere(function ($I) { return $I % 2 === 0; });

        $NewData = array_filter($this->ArrayData, function ($I) { return $I % 2 !== 0; });

        $this->AssertMatches($this->Collection, $NewData, 'Removal');
    }
}
