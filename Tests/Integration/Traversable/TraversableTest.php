<?php

namespace Pinq\Tests\Integration\Traversable;

abstract class TraversableTest extends \Pinq\Tests\PinqTestCase
{
        
    public function TraversablesFor(array $Data)
    {
        return [
            [new \Pinq\Traversable($Data), $Data],
            [(new \Pinq\Traversable($Data))->AsCollection(), $Data],
            [(new \Pinq\Traversable($Data))->AsQueryable(), $Data],
        ];
    }
    
    public function Everything() {
        $Data = [];
        $DataProviders = ['EmptyData', 'OneToTen', 'AssocOneToTen', 'TenRandomStrings'];
        foreach($DataProviders as $Provider) {
            $Data = array_merge($Data, $this->$Provider());
        }
        
        return $Data;
    }
    
    public function EmptyData()
    {
        return $this->TraversablesFor([]);
    }
    
    public function OneToTen()
    {
        return $this->TraversablesFor(range(1, 10));
    }
    
    public function AssocOneToTen()
    {
        return $this->TraversablesFor(array_combine($this->RandomStrings(10), range(1, 10)));
    }
    
    public function TenRandomStrings()
    {
        return $this->TraversablesFor($this->RandomStrings(10));
    }
    
    public function AssocTenRandomStrings()
    {
        return $this->TraversablesFor(array_combine($this->RandomStrings(10), $this->RandomStrings(10)));
    }
    
    private function RandomStrings($Amount)
    {
        $Letters = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890-!@#$%^&*()_';
        $RandomStrings = [];
        for ($Count = 0; $Count < $Amount; $Count++) {
            $RandomStrings[] = substr(str_shuffle($Letters), 0, rand(5, 10));
        }
        
        return $RandomStrings;
    }
    
    final protected function AssertMatches(\Pinq\ITraversable $Traversable, array $Array, $Message = '')
    {
        $this->assertSame($Array, $Traversable->AsArray(), $Message);
    }
        
    final protected function AssertMatchesValues(\Pinq\ITraversable $Traversable, array $Array, $Message = '')
    {
        $this->assertSame(array_values($Array), array_values($Traversable->AsArray()), $Message);
    }
}
