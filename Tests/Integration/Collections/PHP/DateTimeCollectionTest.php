<?php

namespace Pinq\Tests\Integration\Collections\PHP;

class DateTimeCollectionTest extends MemoryCollectionTest
{
    protected function ArrayData()
    {
        return iterator_to_array(new \DatePeriod(new \DateTime('-1 year'), \DateInterval::createFromDateString('3 days'), new \DateTime('+1 year')));
    }

    public function testWeekendDateFilter()
    {
        $Collection = $this->Collection
                ->Where(function (\DateTime $DateTime) { return in_array($DateTime->format('D'), ['Sat', 'Sun']);});

        foreach ($Collection as $DateTime) {
            $this->assertTrue(in_array($DateTime->format('D'), ['Sat', 'Sun']), 'Must be saturday or sunday');
        }
    }

    public function testAggregateValues()
    {
        $this->assertEquals(true, $this->Collection->All(), 'All');

        $this->assertEquals(true, $this->Collection->Any(), 'Any');

        $this->assertEquals(max($this->ArrayData), $this->Collection->Maximum(), 'Max');

        $this->assertEquals(min($this->ArrayData), $this->Collection->Minimum(), 'Min');
    }
}
