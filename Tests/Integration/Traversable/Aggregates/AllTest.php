<?php 

namespace Pinq\Tests\Integration\Traversable\Aggregates;

class AllTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    /**
     * @dataProvider EmptyData
     */
    public function testThatAllReturnsTrueIfEmpty(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertTrue($traversable->all());
    }
    
    public function falseyValues()
    {
        //                                                      V
        return $this->getImplementations([
            1,
            1,
            1,
            1,
            1,
            1,
            10,
            11,
            1,
            1,
            0,
            1,
            1,
            1,
            1
        ]) + 
                $this->getImplementations([
                    'ert',
                    'rgrg',
                    'dgf',
                    'g4g43',
                    'as',
                    'vd',
                    'dw',
                    '',
                    'saav'
                ]) + $this->getImplementations([true, false, true]);
    }
    
    /**
     * @dataProvider FalseyValues
     */
    public function testThatAllReturnsFalseIfThereIsAFalsyValue(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertFalse($traversable->all());
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatAllOperatesCorrectly(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertEquals(
                empty($data) ?: count(array_filter($data)) === count($data),
                $traversable->all());
    }
}