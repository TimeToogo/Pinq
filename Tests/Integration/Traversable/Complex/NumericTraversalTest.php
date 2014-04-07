<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class NumericTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function OneToAHundred()
    {
        return $this->GetImplementations(range(1, 100));
    }
    
    /**
     * @dataProvider OneToAHundred
     */
    public function testOrderByTensThenDescending(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->OrderByAscending(function ($I) { return (int)($I / 10); })
                ->ThenByDescending(function ($I) { return $I; });

        $EquivalentArray = [];
        $Array = [];
        foreach ($Data as $Key => $Value) {
            if ($Value % 10 === 0) {
                $EquivalentArray += array_reverse($Array, true);
                $Array = [];
            }
            $Array[$Key] = $Value;
        }
        $EquivalentArray += array_reverse($Array, true);

        $this->AssertMatches($Traversable, $EquivalentArray);
    }

    /**
     * @dataProvider OneToAHundred
     */
    public function testSimpleAggregation(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertSame(array_sum($Data), $Traversable->Sum());
        $this->assertSame(array_sum($Data) / count($Data), $Traversable->Average());
    }

    /**
     * @dataProvider OneToAHundred
     */
    public function testComplexAggregationQuery(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->AsQueryable()
                ->Where(function ($I) { return $I % 2 === 0; })
                ->OrderByAscending(function ($I) { return -$I; })
                ->GroupBy(function ($I) { return $I % 7; })
                ->Where(function (\Pinq\ITraversable $I) { return $I->Count() % 2 === 0; })
                ->Select(function (\Pinq\ITraversable $Numbers) {
                    return [
                        'First' => $Numbers->First(),
                        'Average' => $Numbers->Average(),
                        'Count' => $Numbers->Count(),
                        'Numbers' => $Numbers->AsArray(),
                    ];
                })
                ->IndexBy(function (array $Values) { return implode(',', $Values['Numbers']); });

        $NewData = array_filter($Data, function ($I) { return $I % 2 === 0; });
        $NewData = array_reverse($NewData, true);
        $Aggregates = [];
        foreach ($NewData as $Key => $Value) {
            $AggregateKey = array_search($Value % 7, array_map(function ($I) { return $I['Key']; }, $Aggregates));
            if ($AggregateKey === false) {
                $AggregateKey = count($Aggregates) + 1;
                $Aggregates[$AggregateKey] = [
                    'Key' => $Value % 7,
                    'First' => $Value,
                    'Average' => null,
                    'Count' => 0,
                    'Numbers' => [],
                ];
            }

            $Aggregates[$AggregateKey]['Numbers'][$Key] = $Value;
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

        $this->AssertMatches($Traversable, $IndexedAggregates, 'Complex Aggregate');
    }
}
