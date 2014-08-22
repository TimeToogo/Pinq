<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class DateTimeTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function dateTimes()
    {
        return $this->implementationsFor(iterator_to_array(new \DatePeriod(
                new \DateTime('-1 year'),
                \DateInterval::createFromDateString('3 days'),
                new \DateTime('+1 year'))));
    }

    /**
     * @dataProvider dateTimes
     */
    public function testWeekendDateFilter(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = $traversable
                ->where(function (\DateTime $dateTime) {
                    return in_array($dateTime->format('D'), ['Sat', 'Sun']);
                });

        foreach ($traversable as $dateTime) {
            $this->assertTrue(
                    in_array($dateTime->format('D'), ['Sat', 'Sun']),
                    'Must be saturday or sunday');
        }
    }

    /**
     * @dataProvider dateTimes
     */
    public function testGroupByDayOfWeekWithDateTimeKeys(\Pinq\ITraversable $traversable, array $data)
    {
        $datesGroupedByDayOfWeek = $traversable
                ->indexBy(function (\DateTime $date) { return $date; })
                ->select(function (\DateTime $date) { return $date->format('Y-m-d'); })
                ->groupBy(function ($string, \DateTime $date) { return $date->format('l'); })
                ->select(function (\Pinq\ITraversable $dateGroup) {
                    return $dateGroup->keys()->asArray();
                })
                ->asArray();

        $expectedDatesGroupedByDayOfWeek = [];
        foreach ($data as $date) {
            $dayOfWeek = $date->format('l');
            if (!isset($expectedDatesGroupedByDayOfWeek[$dayOfWeek])) {
                $expectedDatesGroupedByDayOfWeek[$dayOfWeek] = [];
            }

            $expectedDatesGroupedByDayOfWeek[$dayOfWeek][] = $date;
        }

        $this->assertSame($expectedDatesGroupedByDayOfWeek, $datesGroupedByDayOfWeek);
    }

    /**
     * @dataProvider dateTimes
     */
    public function testAggregateValues(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(true, $traversable->all(), 'All');
        $this->assertEquals(true, $traversable->any(), 'Any');
        $this->assertEquals(max($data), $traversable->maximum(), 'Max');
        $this->assertEquals(min($data), $traversable->minimum(), 'Min');
    }
}
