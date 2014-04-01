<?php

namespace Pinq\Tests\Integration\Collections\PHP;

class StringCollectionTest extends MemoryCollectionTest
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function ArrayData()
    {
        return ['Foo', 'Bar', 'Test', 'Pinq', 'Data', 'Lorem Ipsum', 'Dallas'];
    }

    public function testOrderingMultiple()
    {
        $Collection = $this->Collection
                ->OrderBy(function ($I) { return $I[0]; })
                ->ThenByDescending(function ($I) { return $I[2]; });

        $this->AssertMatches($Collection, [1 => 'Bar', 4 => 'Data', 6 => 'Dallas', 0 => 'Foo', 5 => 'Lorem Ipsum', 3 => 'Pinq', 2 => 'Test']);
    }

    public function testSelectManyQuery()
    {
        $Collection = $this->Collection
                ->SelectMany('str_split')
                ->Select(function ($Char) {
                    return ++$Char;
                })
                ->Implode('');

        $TrueString = '';
        foreach ($this->ArrayData as $I) {
            foreach (str_split($I) as $Char) {
                $TrueString .= ++$Char;
            }
        }

        $this->assertEquals($TrueString, $Collection);
    }

    public function testAggregateValuesString()
    {
        $this->assertEquals(true, $this->Collection->All(), 'All');

        $this->assertEquals(true, $this->Collection->Any(), 'Any');

        $this->assertEquals(array_sum(array_map('strlen', $this->ArrayData)) / count($this->ArrayData), $this->Collection->Average('strlen'), 'Average string length');

        $this->assertEquals(array_sum(array_map('strlen', $this->ArrayData)), $this->Collection->Sum('strlen'), 'Sum string length');

        $this->assertEquals(array_unique($this->ArrayData), $this->Collection->Unique()->AsArray(), 'Unique');

        $this->assertEquals(implode('- -- -', $this->ArrayData), $this->Collection->Implode('- -- -'), 'String implode');
    }
}
