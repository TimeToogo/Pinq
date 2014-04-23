<?php 

namespace Pinq\Tests\Integration\Traversable;

class WhereTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatExecutionIsDeferred(\Pinq\ITraversable $traversable, array $data)
    {
        $this->assertThatExecutionIsDeferred([$traversable, 'Where']);
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatWhereTrueDoesNotFilterAnyData(\Pinq\ITraversable $numbers, array $data)
    {
        $allNumbers = 
                $numbers->where(function () {
                    return true;
                });
        $this->assertMatches($allNumbers, $data);
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatWhereFalseFiltersAllItems(\Pinq\ITraversable $numbers, array $data)
    {
        $noNumbers = 
                $numbers->where(function () {
                    return false;
                });
        $this->assertMatches($noNumbers, []);
    }
    
    /**
     * @dataProvider AssocOneToTen
     */
    public function testThatElementsAreFilteredFromTraversableAndPreserveKeys(\Pinq\ITraversable $numbers, array $data)
    {
        $predicate = 
                function ($i) {
                    return $i % 2 === 0;
                };
        $evenNumbers = $numbers->where($predicate);
        
        foreach ($data as $key => $value) {
            if (!$predicate($value)) {
                unset($data[$key]);
            }
        }
        
        $this->assertMatches($evenNumbers, $data);
    }
}