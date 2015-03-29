<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Tests\Integration\Queries\QueryBuildingTestsTrait;

class EnglishDSLProviderTest extends DSLCompilationProviderTest
{
    use QueryBuildingTestsTrait;

    protected function compilerConfiguration()
    {
        return new Implementation\English\Configuration();
    }

    protected function valuesAsSelfQuery()
    {
        return <<<'ENG'
Get the elements as itself
ENG;
    }

    protected function valuesAsArrayQuery()
    {
        return <<<'ENG'
Get the elements as an array
ENG;
    }

    protected function valuesAsIteratorQuery()
    {
        return <<<'ENG'
Get the elements as an array compatible iterator
ENG;
    }

    protected function valuesAsTrueIteratorQuery()
    {
        return <<<'ENG'
Get the elements as an iterator
ENG;
    }

    protected function valuesAsCollectionQuery()
    {
        return <<<'ENG'
Get the elements as a collection
ENG;
    }

    protected function valuesAsTraversableQuery()
    {
        return <<<'ENG'
Get the elements as a traversable
ENG;
    }

    protected function offsetGetQuery()
    {
        return <<<'ENG'
Get the index
ENG;
    }

    protected function offsetIssetQuery()
    {
        return <<<'ENG'
Get whether the index is set
ENG;
    }

    protected function containsQuery()
    {
        return <<<'ENG'
Whether contains the parameter
ENG;
    }

    protected function isEmptyQuery()
    {
        return <<<'ENG'
Get whether there are no elements
ENG;
    }

    protected function countQuery()
    {
        return <<<'ENG'
Get the amount of elements
ENG;
    }

    protected function firstQuery()
    {
        return <<<'ENG'
Get the first element
ENG;
    }

    protected function lastQuery()
    {
        return <<<'ENG'
Get the last element
ENG;
    }

    protected function aggregateQuery()
    {
        return <<<'ENG'
Get the values aggregated according to the function: { return ($a + $s); } with parameters: [$this]
ENG;
    }

    protected function allQuery()
    {
        return <<<'ENG'
Whether all of the values are truthy
ENG;
    }

    protected function anyQuery()
    {
        return <<<'ENG'
Whether any of the values are truthy
ENG;
    }

    protected function maximumQuery()
    {
        return <<<'ENG'
Get the maximum value
ENG;
    }

    protected function minimumQuery()
    {
        return <<<'ENG'
Get the minimum value
ENG;
    }

    protected function sumQuery()
    {
        return <<<'ENG'
Get the sum of the values
ENG;
    }

    protected function averageQuery()
    {
        return <<<'ENG'
Get the average of the values
ENG;
    }

    protected function implodeQuery()
    {
        return <<<'ENG'
Get as a delimited string
ENG;
    }

    protected function setIndexQuery()
    {
        return <<<'ENG'
Set the index
ENG;
    }

    protected function unsetIndexQuery()
    {
        return <<<'ENG'
Remove the index
ENG;
    }

    protected function applyQuery()
    {
        return <<<'ENG'
Update the values according to the function: { $i *= 10; } with parameters: [$this]
ENG;
    }

    protected function addRangeQuery()
    {
        return <<<'ENG'
Add the following values: [array or iterator]
ENG;
    }

    protected function addSubscopeQuery()
    {
        return <<<'ENG'
Add the following values: [
    Filter according to: { return ($i > 5); } with parameters: [$this]
]
ENG;
    }

    protected function addQuery()
    {
        return <<<'ENG'
Add the following values: [single value]
ENG;
    }

    protected function removeRangeQuery()
    {
        return <<<'ENG'
Remove the following values: [array or iterator]
ENG;
    }

    protected function removeSubscopeQuery()
    {
        return <<<'ENG'
Remove the following values: [
    Filter according to: { return ($i < 3); } with parameters: [$this]
]
ENG;
    }

    protected function removeWhereQuery()
    {
        return <<<'ENG'
Remove the elements according to: { return true; } with parameters: [$this]
ENG;
    }

    protected function removeQuery()
    {
        return <<<'ENG'
Remove the following values: [single value]
ENG;
    }

    protected function clearQuery()
    {
        return <<<'ENG'
Remove all the elements
ENG;
    }

    protected function joinApplyQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] filtered according to: { return true; } with parameters: [$this] and update the outer values according to: { $o *= $i; } with parameters: [$this]
ENG;
    }

    protected function whereQuery()
    {
        return <<<'ENG'
Filter according to: { return true; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function orderByAscendingQuery()
    {
        return <<<'ENG'
Order according to: { return $i; } with parameters: [$this] asc or desc
Get the elements as itself
ENG;
    }

    protected function orderByDescendingQuery()
    {
        return <<<'ENG'
Order according to: { return $i; } with parameters: [$this] asc or desc
Get the elements as itself
ENG;
    }

    protected function orderByAscendingWithThenByAscendingQuery()
    {
        return <<<'ENG'
Order according to: { return $i; } with parameters: [$this] asc or desc, { return $i; } with parameters: [$this] asc or desc
Get the elements as itself
ENG;
    }

    protected function orderByAscendingWithThenByDescendingQuery()
    {
        return <<<'ENG'
Order according to: { return $i; } with parameters: [$this] asc or desc, { return $i; } with parameters: [$this] asc or desc
Get the elements as itself
ENG;
    }

    protected function skipQuery()
    {
        return <<<'ENG'
Starting from and up to the specified element
Get the elements as itself
ENG;
    }

    protected function takeQuery()
    {
        return <<<'ENG'
Starting from and up to the specified element
Get the elements as itself
ENG;
    }

    protected function indexByQuery()
    {
        return <<<'ENG'
Index according to: { return $i; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function keysQuery()
    {
        return <<<'ENG'
Use keys
Get the elements as itself
ENG;
    }

    protected function reindexQuery()
    {
        return <<<'ENG'
Reindex keys
Get the elements as itself
ENG;
    }

    protected function groupByQuery()
    {
        return <<<'ENG'
Group according to: { return $i; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function unfilteredJoinQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function joinOnQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] filtered according to: { return ($o === $i); } with parameters: [$this] and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function joinOnEqualityQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] filtered according to: { return $o; } with parameters: [$this] equaling { return $i; } with parameters: [$this] and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function joinWithDefaultQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] with default values and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function joinTwoWithDefaultsQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] with default values and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function joinToSubScopeQuery()
    {
        return <<<'ENG'
Join with: [
    Order according to: { return $i; } with parameters: [$this] asc or desc
    Map according to: { return $i; } with parameters: [$this]
    Starting from and up to the specified element
] and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function unfilteredGroupJoinWithDefaultQuery()
    {
        return <<<'ENG'
Join with: [array or iterator] into groups and correlate the values according to: { return [$o, $i]; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function uniqueQuery()
    {
        return <<<'ENG'
Only unique values
Get the elements as itself
ENG;
    }

    protected function selectQuery()
    {
        return <<<'ENG'
Map according to: { return $i; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function selectManyQuery()
    {
        return <<<'ENG'
Map and flatten according to: { return $i; } with parameters: [$this]
Get the elements as itself
ENG;
    }

    protected function appendQuery()
    {
        return <<<'ENG'
Append with: [array or iterator]
Get the elements as itself
ENG;
    }

    protected function whereInQuery()
    {
        return <<<'ENG'
Where contained in: [array or iterator]
Get the elements as itself
ENG;
    }

    protected function exceptQuery()
    {
        return <<<'ENG'
Where not contained in: [array or iterator]
Get the elements as itself
ENG;
    }

    protected function unionQuery()
    {
        return <<<'ENG'
The union with: [array or iterator]
Get the elements as itself
ENG;
    }

    protected function intersectQuery()
    {
        return <<<'ENG'
The intersection with: [array or iterator]
Get the elements as itself
ENG;
    }

    protected function differenceQuery()
    {
        return <<<'ENG'
The difference from: [array or iterator]
Get the elements as itself
ENG;
    }

    protected function intersectWithFilteredSubScopeQuery()
    {
        return <<<'ENG'
The intersection with: [
    Filter according to: { return true; } with parameters: [$this]
]
Get the elements as itself
ENG;
    }

    protected function nestedOperationsQuery()
    {
        return <<<'ENG'
Append with: [
    Where contained in: [
        Where not contained in: [
            The union with: [
                The intersection with: [
                    The difference from: [
                        Starting from and up to the specified element
                    ]
                ]
            ]
        ]
    ]
]
Get the elements as itself
ENG;
    }

    protected function exampleFromDocsQuery()
    {
        return <<<'ENG'
Filter according to: { return ($row['age'] <= 50); } with parameters: [$this]
Order according to: { return $row['firstName']; } with parameters: [$this] asc or desc, { return $row['lastName']; } with parameters: [$this] asc or desc
Starting from and up to the specified element
Index according to: { return $row['phoneNumber']; } with parameters: [$this]
Map according to: { return ['fullName' => (($row['firstName'] . ' ') . $row['lastName']), 'address' => $row['address'], 'dateOfBirth' => $row['dateOfBirth']]; } with parameters: [$this]
Get the elements as itself
ENG;
    }
}
