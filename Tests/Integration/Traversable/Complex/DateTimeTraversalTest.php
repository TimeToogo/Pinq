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
     * @dataProvider DateTimes
     */
    public function testWeekendDateFilter(\Pinq\ITraversable $traversable, array $data)
    {
        $traversable = 
                $traversable->where(function (\DateTime $dateTime) {
                    return in_array($dateTime->format('D'), ['Sat', 'Sun']);
                });
        
        foreach ($traversable as $dateTime) {
            $this->assertTrue(
                    in_array($dateTime->format('D'), ['Sat', 'Sun']),
                    'Must be saturday or sunday');
        }
    }
    
    /**
     * @dataProvider DateTimes
     */
    public function testAggregateValues(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(true, $traversable->all(), 'All');
        $this->assertEquals(true, $traversable->any(), 'Any');
        $this->assertEquals(max($data), $traversable->maximum(), 'Max');
        $this->assertEquals(min($data), $traversable->minimum(), 'Min');
    }
}