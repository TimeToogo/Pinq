<?php 

namespace Pinq\Tests\Integration;

abstract class DataTest extends \Pinq\Tests\PinqTestCase
{
    private $implementationCounter = 0;
    
    protected abstract function implementationsFor(array $data);
    
    protected final function getImplementations(array $data)
    {
        $implementations = [];
        
        foreach ($this->implementationsFor($data) as $implementation) {
            $implementations[++$this->implementationCounter] = $implementation;
        }
        
        return $implementations;
    }
    
    public function everything()
    {
        $data = [];
        $dataProviders = [
            'EmptyData',
            'OneToTen',
            'OneToTenTwice',
            'AssocOneToTen',
            'TenRandomStrings'
        ];
        
        foreach ($dataProviders as $provider) {
            $data = array_merge($data, $this->{$provider}());
        }
        
        return $data;
    }
    
    public function emptyData()
    {
        return $this->getImplementations([]);
    }
    
    public function oneToTen()
    {
        return $this->getImplementations(range(1, 10));
    }
    
    public function oneToTenTwice()
    {
        return $this->getImplementations(array_merge(range(1, 10), range(1, 10)));
    }
    
    public function assocOneToTen()
    {
        return $this->getImplementations(array_combine($this->randomStrings(10), range(1, 10)));
    }
    
    public function tenRandomStrings()
    {
        return $this->getImplementations($this->randomStrings(10));
    }
    
    public function assocTenRandomStrings()
    {
        return $this->getImplementations(array_combine($this->randomStrings(10), $this->randomStrings(10)));
    }
    
    private function randomStrings($amount)
    {
        $letters = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890-!@#$%^&*()_';
        $randomStrings = [];
        
        for ($count = 0; $count < $amount; $count++) {
            $randomStrings[] = substr(str_shuffle($letters), 0, rand(5, 10));
        }
        
        return $randomStrings;
    }
    
    protected final function assertMatches(\Pinq\ITraversable $traversable, array $array, $message = '')
    {
        $this->assertSame($array, $traversable->asArray(), $message);
    }
    
    protected final function assertMatchesValues(\Pinq\ITraversable $traversable, array $array, $message = '')
    {
        $this->assertSame(
                array_values($array),
                array_values($traversable->asArray()),
                $message);
    }
}