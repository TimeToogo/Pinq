<?php

namespace Pinq\Tests\Integration;

abstract class DataTest extends \Pinq\Tests\PinqTestCase
{
    private $ImplementationCounter = 0;
        
    protected abstract function ImplementationsFor(array $Data);
    
    final protected function GetImplementations(array $Data) 
    {
        $Implementations = [];
        foreach ($this->ImplementationsFor($Data) as $Implementation) {
            $Implementations[++$this->ImplementationCounter] = $Implementation;
        }
        
        return $Implementations;
    }
    
    public function Everything() 
    {
        $Data = [];
        $DataProviders = ['EmptyData', 'OneToTen', 'OneToTenTwice', 'AssocOneToTen', 'TenRandomStrings'];
        
        foreach($DataProviders as $Provider) {
            $Data = array_merge($Data, $this->$Provider());
        }
        
        return $Data;
    }
    
    public function EmptyData()
    {
        return $this->GetImplementations([]);
    }
    
    public function OneToTen()
    {
        return $this->GetImplementations(range(1, 10));
    }
    
    public function OneToTenTwice()
    {
        return $this->GetImplementations(array_merge(range(1, 10), range(1, 10)));
    }
    
    public function AssocOneToTen()
    {
        return $this->GetImplementations(array_combine($this->RandomStrings(10), range(1, 10)));
    }
    
    public function TenRandomStrings()
    {
        return $this->GetImplementations($this->RandomStrings(10));
    }
    
    public function AssocTenRandomStrings()
    {
        return $this->GetImplementations(array_combine($this->RandomStrings(10), $this->RandomStrings(10)));
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
