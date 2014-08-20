<?php

namespace Pinq\Tests\Integration\Traversable\Complex;

class ArrayTraversalTest extends \Pinq\Tests\Integration\Traversable\TraversableTest
{
    public function people()
    {
        return $this->getImplementations([
            ['firstName' => 'Zoe', 'lastName' => 'Black', 'age' => 32, 'sex' => 'Female'],
            ['firstName' => 'Alex', 'lastName' => 'Katter', 'age' => 24, 'sex' => 'Male'],
            ['firstName' => 'Daniel', 'lastName' => 'Farmer', 'age' => 54, 'sex' => 'Male'],
            ['firstName' => 'Casy', 'lastName' => 'Denali', 'age' => 26, 'sex' => 'Female'],
            ['firstName' => 'Dave', 'lastName' => 'Desopolous', 'age' => 21, 'sex' => 'Male'],
            ['firstName' => 'Hugo', 'lastName' => 'Tesserat', 'age' => 43, 'sex' => 'Male'],
            ['firstName' => 'Sandy', 'lastName' => 'Williams', 'age' => 34, 'sex' => 'Female'],
            ['firstName' => 'Beth', 'lastName' => 'Baronksy', 'age' => 39, 'sex' => 'Female'],
            ['firstName' => 'David', 'lastName' => 'Faller', 'age' => 63, 'sex' => 'Male'],
            ['firstName' => 'Daniel', 'lastName' => 'Dekresta', 'age' => 32, 'sex' => 'Male']
        ]);
    }

    /**
     * @dataProvider people
     */
    public function testOrderByMultipleColumns(\Pinq\ITraversable $traversable, array $data)
    {
        $orderedNames = $traversable
                ->orderByAscending(function ($i) { return $i['firstName']; })
                ->thenByDescending(function ($i) { return $i['lastName']; })
                ->implode(':', function ($i) { return $i['firstName'] . ' ' . $i['lastName']; });

        $this->assertEquals(
                'Alex Katter:Beth Baronksy:Casy Denali:Daniel Farmer:Daniel Dekresta:Dave Desopolous:David Faller:Hugo Tesserat:Sandy Williams:Zoe Black',
                $orderedNames);
    }

    /**
     * @dataProvider people
     */
    public function testWhereCondition(\Pinq\ITraversable $traversable, array $data)
    {
        $fileteredNames = $traversable
                ->where(function ($i) { return strpos($i['firstName'], 'D') !== false; })
                ->implode(':', function ($i) { return $i['firstName'] . ' ' . $i['lastName']; });

        $this->assertEquals(
                'Daniel Farmer:Dave Desopolous:David Faller:Daniel Dekresta',
                $fileteredNames);
    }

    /**
     * @dataProvider people
     */
    public function testGroupJoinToSelfWithCondition(\Pinq\ITraversable $traversable, array $data)
    {
        $joinedlastNames = $traversable
                ->groupJoin($traversable)
                    ->onEquality(
                            function ($outer) { return $outer['firstName'][0]; },
                            function ($inner) { return $inner['lastName'][0]; })
                    ->to(function ($person, \Pinq\ITraversable $joinedPeople) {
                        return $person['firstName'] . '{' .
                                $joinedPeople->implode(
                                        ',',
                                        function ($person) {
                                            return $person['lastName'];
                                        }) . '}';
                    })
                ->implode(':');

        $this->assertEquals(
                'Zoe{}:Alex{}:Daniel{Denali,Desopolous,Dekresta}:Casy{}:Dave{Denali,Desopolous,Dekresta}:' .
                'Hugo{}:Sandy{}:Beth{Black,Baronksy}:David{Denali,Desopolous,Dekresta}:Daniel{Denali,Desopolous,Dekresta}',
                $joinedlastNames);
    }

    /**
     * @dataProvider people
     */
    public function testGroupByWithArrayKey(\Pinq\ITraversable $traversable, array $data)
    {
        $ageGroupData = $traversable
                ->groupBy(function ($i) {
                    return [
                        'sex' => $i['sex'],
                        'ageGroup' => floor($i['age'] / 10) * 10
                    ];
                })
                ->orderByAscending(function (\Pinq\ITraversable $group, $key) { return $key['ageGroup']; })
                ->thenByAscending(function (\Pinq\ITraversable $group, $key) { return $key['sex']; })
                ->select(function (\Pinq\ITraversable $group, $key) {
                    $ageGroup = $key['ageGroup'] . '+';
                    $sex = $key['sex'];

                    return sprintf('%s(%s){%s}', $ageGroup, $sex, $group->implode(',', function ($i) {
                        return $i['firstName'];
                    }));
                })
                ->implode(':');

        $this->assertEquals(
                '20+(Female){Casy}:20+(Male){Alex,Dave}:30+(Female){Zoe,Sandy,Beth}:30+(Male){Daniel}:' .
                '40+(Male){Hugo}:50+(Male){Daniel}:60+(Male){David}',
                $ageGroupData);
    }
}
