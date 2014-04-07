<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class DateTimeTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function DateTimes()
    {
        return $this->ImplementationsFor(iterator_to_array(
                new \DatePeriod(new \DateTime('-1 year'), \DateInterval::createFromDateString('3 days'), new \DateTime('+1 year'))));
    }
    
    /**
     * @dataProvider DateTimes
     */
    public function testWeekendDateFilter(\Pinq\ITraversable $Traversable, array $Data)
    {
        $Traversable = $Traversable
                ->Where(function (\DateTime $DateTime) { return in_array($DateTime->format('D'), ['Sat', 'Sun']);});

        foreach ($Traversable as $DateTime) {
            $this->assertTrue(in_array($DateTime->format('D'), ['Sat', 'Sun']), 'Must be saturday or sunday');
        }
    }

    /**
     * @dataProvider DateTimes
     */
    public function testAggregateValues(\Pinq\ITraversable $Traversable, array $Data)
    {
        $this->assertEquals(true, $Traversable->All(), 'All');

        $this->assertEquals(true, $Traversable->Any(), 'Any');

        $this->assertEquals(max($Data), $Traversable->Maximum(), 'Max');

        $this->assertEquals(min($Data), $Traversable->Minimum(), 'Min');
    }
}
