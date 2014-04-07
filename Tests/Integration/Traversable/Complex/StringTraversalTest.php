<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class StringTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function SomeStrings()
    {
        return $this->ImplementationsFor(['Foo', 'Bar', 'Test', 'Pinq', 'Data', 'Lorem Ipsum', 'Dallas']);
    }

    /**
     * @dataProvider SomeStrings
     */
    public function testOrderingMultiple(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->OrderByAscending(function ($I) { return $I[0]; })
                ->ThenByDescending(function ($I) { return $I[2]; });

        $this->AssertMatches($Traversable, [1 => 'Bar', 4 => 'Data', 6 => 'Dallas', 0 => 'Foo', 5 => 'Lorem Ipsum', 3 => 'Pinq', 2 => 'Test']);
    }

    /**
     * @dataProvider SomeStrings
     */
    public function testSelectManyQuery(\Pinq\ITraversable $Traversable, array $Data)
    {
        $String = $Traversable
                ->SelectMany('str_split')
                ->Select(function ($Char) {
                    return ++$Char;
                })
                ->Implode('');

        $TrueString = '';
        foreach ($Data as $I) {
            foreach (str_split($I) as $Char) {
                $TrueString .= ++$Char;
            }
        }

        $this->assertEquals($TrueString, $String);
    }

    /**
     * @dataProvider SomeStrings
     */
    public function testAggregateValuesString(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(true, $Traversable->All(), 'All');

        $this->assertEquals(true, $Traversable->Any(), 'Any');

        $this->assertEquals(array_sum(array_map('strlen', $Data)) / count($Data), $Traversable->Average('strlen'), 'Average string length');

        $this->assertEquals(array_sum(array_map('strlen', $Data)), $Traversable->Sum('strlen'), 'Sum string length');

        $this->assertEquals(array_unique($Data), $Traversable->Unique()->AsArray(), 'Unique');

        $this->assertEquals(implode('- -- -', $Data), $Traversable->Implode('- -- -'), 'String implode');
    }
}
