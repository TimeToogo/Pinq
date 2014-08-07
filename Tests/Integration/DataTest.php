<?php

namespace Pinq\Tests\Integration;

abstract class DataTest extends \Pinq\Tests\PinqTestCase
{
    private $implementationCounter = 0;

    abstract protected function implementationsFor(array $data);

    final protected function getImplementations(array $data)
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

    public function theImplementations()
    {
        return $this->getImplementations([1, 2, 3]);
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
    
    final protected function makeRefs(array $array)
    {
        static $refs = [];
        
        foreach($array as $key => &$value) {
            $refs[] =& $value;
        }
        
        return $array;
    }

    final protected function assertMatches(\Pinq\ITraversable $traversable, array $array, $message = '')
    {
        $firstIterationArray = [];
        foreach($traversable as $key => $value) {
            $firstIterationArray[$key] = $value;
        }
        $secondIterationArray = [];
        foreach($traversable as $key => $value) {
            $secondIterationArray[$key] = $value;
        }
        
        //Ignore keys as may be incompatible with foreach
        $trueIteratorArray = [];
        foreach($traversable->getTrueIterator() as $value) {
            $trueIteratorArray[] = $value;
        }
        
        $explicitArray = $traversable->asArray();
        
        $this->assertSame($array, $firstIterationArray, $message);
        $this->assertSame($array, $secondIterationArray, $message);
        $this->assertSame($array, $explicitArray, $message);
        $this->assertSame(array_values($array), $trueIteratorArray, $message);
    }

    final protected function assertMatchesValues(\Pinq\ITraversable $traversable, array $array, $message = '')
    {
        $firstIterationArray = [];
        foreach($traversable as $key => $value) {
            $firstIterationArray[] = $value;
        }
        $secondIterationArray = [];
        foreach($traversable as $key => $value) {
            $secondIterationArray[] = $value;
        }

        $explicitArray = array_values($traversable->asArray());
        
        $array =  array_values($array);
        
        $this->assertSame($array, $firstIterationArray, $message);
        $this->assertSame($array, $secondIterationArray, $message);
        $this->assertSame($array, $explicitArray, $message);
    }
}
