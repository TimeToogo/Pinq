<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class ArrayTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function people()
    {
        return $this->getImplementations([
            ['FirstName' => 'Zoe', 'LastName' => 'Black', 'Age' => 32, 'Sex' => 'Female'],
            ['FirstName' => 'Alex', 'LastName' => 'Katter', 'Age' => 24, 'Sex' => 'Male'],
            ['FirstName' => 'Daniel', 'LastName' => 'Farmer', 'Age' => 54, 'Sex' => 'Male'],
            ['FirstName' => 'Casy', 'LastName' => 'Denali', 'Age' => 26, 'Sex' => 'Female'],
            ['FirstName' => 'Dave', 'LastName' => 'Desopolous', 'Age' => 21, 'Sex' => 'Male'],
            ['FirstName' => 'Hugo', 'LastName' => 'Tesserat', 'Age' => 43, 'Sex' => 'Male'],
            ['FirstName' => 'Sandy', 'LastName' => 'Williams', 'Age' => 34, 'Sex' => 'Female'],
            ['FirstName' => 'Beth', 'LastName' => 'Baronksy', 'Age' => 39, 'Sex' => 'Female'],
            ['FirstName' => 'David', 'LastName' => 'Faller', 'Age' => 63, 'Sex' => 'Male'],
            ['FirstName' => 'Daniel', 'LastName' => 'Dekresta', 'Age' => 32, 'Sex' => 'Male']
        ]);
    }

    /**
     * @dataProvider People
     */
    public function testOrderByMultipleColumns(\Pinq\ITraversable $traversable, array $data)
    {
        $orderedNames = $traversable
                ->orderByAscending(function ($i) { return $i['FirstName']; })
                ->thenByDescending(function ($i) { return $i['LastName']; })
                ->implode(':', function ($i) { return $i['FirstName'] . ' ' . $i['LastName']; });

        $this->assertEquals(
                'Alex Katter:Beth Baronksy:Casy Denali:Daniel Farmer:Daniel Dekresta:Dave Desopolous:David Faller:Hugo Tesserat:Sandy Williams:Zoe Black',
                $orderedNames);
    }

    /**
     * @dataProvider People
     */
    public function testWhereCondition(\Pinq\ITraversable $traversable, array $data)
    {
        $fileteredNames = $traversable
                ->where(function ($i) { return strpos($i['FirstName'], 'D') !== false; })
                ->implode(':', function ($i) { return $i['FirstName'] . ' ' . $i['LastName']; });

        $this->assertEquals(
                'Daniel Farmer:Dave Desopolous:David Faller:Daniel Dekresta',
                $fileteredNames);
    }

    /**
     * @dataProvider People
     */
    public function testGroupJoinToSelfWithCondition(\Pinq\ITraversable $traversable, array $data)
    {
        $joinedLastNames = $traversable
                ->groupJoin($traversable)
                ->onEquality(function ($outer) { return $outer['FirstName'][0]; }, function ($inner) { return $inner['LastName'][0]; })
                ->to(function ($person, \Pinq\ITraversable $joinedPeople) {
                    return $person['FirstName'] . '{' .
                            $joinedPeople->implode(
                                    ',',
                                    function ($person) {
                                        return $person['LastName'];
                                    }) . '}';
                })
                ->implode(':');

        $this->assertEquals(
                'Zoe{}:Alex{}:Daniel{Denali,Desopolous,Dekresta}:Casy{}:Dave{Denali,Desopolous,Dekresta}:' .
                'Hugo{}:Sandy{}:Beth{Black,Baronksy}:David{Denali,Desopolous,Dekresta}:Daniel{Denali,Desopolous,Dekresta}',
                $joinedLastNames);
    }

    /**
     * @dataProvider People
     */
    public function testGroupMultipleGroupBy(\Pinq\ITraversable $traversable, array $data)
    {
        $joinedLastNames = $traversable
                ->groupBy(function ($i) { return $i['Sex']; })
                ->andBy(function ($i) { return floor($i['Age'] / 10); })
                ->orderByAscending(function (\Pinq\ITraversable $group) { return $group->first()['Age']; })
                ->thenByAscending(function (\Pinq\ITraversable $group) { return $group->first()['Sex']; })
                ->select(function (\Pinq\ITraversable $group) {
                    $ageGroup = floor($group->first()['Age'] / 10) * 10 . '+';
                    $sex = $group->first()['Sex'];

                    return sprintf('%s(%s){%s}', $ageGroup, $sex, $group->implode(',', function ($i) {
                        return $i['FirstName'];
                    }));
                })
                ->implode(':');

        $this->assertEquals(
                '20+(Male){Alex,Dave}:20+(Female){Casy}:30+(Female){Zoe,Sandy,Beth}:30+(Male){Daniel}:' .
                '40+(Male){Hugo}:50+(Male){Daniel}:60+(Male){David}',
                $joinedLastNames);
    }
}
