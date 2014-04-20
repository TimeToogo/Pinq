<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class ArrayTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function People()
    {
        return $this->GetImplementations([
            ['FirstName' => 'Zoe', 'LastName' => 'Black', 'Age' => 32, 'Sex' => 'Female'],
            ['FirstName' => 'Alex', 'LastName' => 'Katter', 'Age' => 24, 'Sex' => 'Male'],
            ['FirstName' => 'Daniel', 'LastName' => 'Farmer', 'Age' => 54, 'Sex' => 'Male'],
            ['FirstName' => 'Casy', 'LastName' => 'Denali', 'Age' => 26, 'Sex' => 'Female'],
            ['FirstName' => 'Dave', 'LastName' => 'Desopolous', 'Age' => 21, 'Sex' => 'Male'],
            ['FirstName' => 'Hugo', 'LastName' => 'Tesserat', 'Age' => 43, 'Sex' => 'Male'],
            ['FirstName' => 'Sandy', 'LastName' => 'Williams', 'Age' => 34, 'Sex' => 'Female'],
            ['FirstName' => 'Beth', 'LastName' => 'Baronksy', 'Age' => 39, 'Sex' => 'Female'],
            ['FirstName' => 'David', 'LastName' => 'Faller', 'Age' => 63, 'Sex' => 'Male'],
            ['FirstName' => 'Daniel', 'LastName' => 'Dekresta', 'Age' => 32, 'Sex' => 'Male'],
        ]);
    }
    
    /**
     * @dataProvider People
     */
    public function testOrderByMultipleColumns(\Pinq\ITraversable $Traversable, array $Data)
    {
        $OrderedNames = $Traversable
                ->OrderByAscending(function ($I) { return $I['FirstName']; })
                ->ThenByDescending(function ($I) { return $I['LastName']; })
                ->Implode(':', function ($I) { return $I['FirstName'] . ' ' . $I['LastName']; });

        $this->assertEquals('Alex Katter:Beth Baronksy:Casy Denali:Daniel Farmer:Daniel Dekresta:Dave Desopolous:David Faller:Hugo Tesserat:Sandy Williams:Zoe Black', $OrderedNames);
    }
    
    /**
     * @dataProvider People
     */
    public function testWhereCondition(\Pinq\ITraversable $Traversable, array $Data)
    {
        $FileteredNames = $Traversable
                ->Where(function ($I) { return strpos($I['FirstName'], 'D') !== false; })
                ->Implode(':', function ($I) { return $I['FirstName'] . ' ' . $I['LastName']; });

        $this->assertEquals('Daniel Farmer:Dave Desopolous:David Faller:Daniel Dekresta', $FileteredNames);
    }
    
    /**
     * @dataProvider People
     */
    public function testGroupJoinToSelfWithCondition(\Pinq\ITraversable $Traversable, array $Data)
    {
        $JoinedLastNames = $Traversable
                ->GroupJoin($Traversable)
                ->OnEquality(function ($Outer) { return $Outer['FirstName'][0]; }, function ($Inner) { return $Inner['LastName'][0]; })
                ->To(function ($Person, \Pinq\ITraversable $JoinedPeople) { 
                    return $Person['FirstName'] . '{' . $JoinedPeople->Implode(',', function ($Person) { return $Person['LastName']; }) . '}'; 
                })
                ->Implode(':');

        $this->assertEquals(
                'Zoe{}:Alex{}:Daniel{Denali,Desopolous,Dekresta}:Casy{}:Dave{Denali,Desopolous,Dekresta}:' . 
                'Hugo{}:Sandy{}:Beth{Black,Baronksy}:David{Denali,Desopolous,Dekresta}:Daniel{Denali,Desopolous,Dekresta}', 
                $JoinedLastNames);
    }
    
    /**
     * @dataProvider People
     */
    public function testGroupMultipleGroupBy(\Pinq\ITraversable $Traversable, array $Data)
    {
        $JoinedLastNames = $Traversable
                ->GroupBy(function ($I) { return $I['Sex']; })
                ->AndBy(function ($I) { return floor($I['Age'] / 10); })
                ->OrderByAscending(function (\Pinq\ITraversable $Group) { return $Group->First()['Age']; })
                ->ThenByAscending(function (\Pinq\ITraversable $Group) { return $Group->First()['Sex']; })
                ->Select(function (\Pinq\ITraversable $Group) { 
                    $AgeGroup = (floor($Group->First()['Age'] / 10) * 10) . '+';
                    $Sex = $Group->First()['Sex'];
                    return sprintf('%s(%s){%s}', $AgeGroup, $Sex, $Group->Implode(',', function ($I) { return $I['FirstName']; })); 
                })
                ->Implode(':');

        $this->assertEquals(
                '20+(Male){Alex,Dave}:20+(Female){Casy}:30+(Female){Zoe,Sandy,Beth}:30+(Male){Daniel}:' .
                '40+(Male){Hugo}:50+(Male){Daniel}:60+(Male){David}', 
                $JoinedLastNames);
    }
}
