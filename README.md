PHP Integrated query
====================
[![Build status](https://api.travis-ci.org/TimeToogo/Pinq.png)](https://travis-ci.org/TimeToogo/Pinq)
[![Code quality](https://scrutinizer-ci.com/g/TimeToogo/Pinq/badges/quality-score.png?s=ddce8f86d3192ab4ca1134aa98e17ab7340014f7)](https://scrutinizer-ci.com/g/TimeToogo/Pinq)


What is Pinq?
=============
Based off the .NET's [Linq](http://msdn.microsoft.com/en-us/library/bb397926.aspx), Pinq unifies querying across [arrays/iterators](#pinq-examples) and [external data sources](#pinq-external-source), in a single readable and concise [fluent API](#pinq-api).

Installation
============
Add package to you composer.json
```json
{
    "require": {
        "timetoogo/pinq": "dev-master"
    }
}
```

<a name="pinq-api"></a>Query and collection API
===============================================

ITraversable
============

The `ITraversable` interface represents a range of values that are able to be queried upon with the following methods:

**Queries**
 - `Where` - Filters the values according to the supplied predicate
 - `OrderBy/OrderByAscending/OrderByDescending` - Orders the values according the supplied order direction
 - `ThenBy/ThenByAscending/ThenByDescending` - Subsequently orders the values according the supplied order direction
 - `Skip` - Skip the supplied amount of values
 - `Take` - Limits the amount of values by the supplied amount
 - `Slice` - Retrieve a slice of values according to the specified offset and amount
 - `IndexBy` - Index the values according to the supplied mapping function
 - `GroupBy` - Groups the values according the supplied mapping function
 - `Unique` - Only return unique values
 - `Select` - Map the values according to the supplied function
 - `SelectMany` - Map the values according to the supplied function and merge the results
 - `First` - Returns the first value or null if empty
 - `Last` - Returns the last value or null if empty
 - `Contains` - Returns if the supplied value is present in the values
 - `offsetGet` - Returns a value at the supplied index
 - `offsetExists` - Whether a value exists for the supplied index

**Set/List Operations**
 - `Append` - All values present in either the original or supplied values
 - `WhereIn` - All values present in both the original and supplied values
 - `Except` - All values present in the original but not in the supplied values
 - `Union` - Unique values present in either the original or supplied values
 - `Intersect` - Unique values present in both the original and supplied values
 - `Difference` - Unique values present in the original but not in the supplied values
 - `offsetSet` - Sets a value to supplied index
 - `offsetUnset` - Removes any value at the supplied index

**Aggregates**
 - `Count` - The amount of values
 - `Exists` - Whether there are any values
 - `Aggregate` - Aggregates the values according to the supplied
 - `Maximum` - The maximum value
 - `Minimum` - The minimum value
 - `Sum` - The sum of all the values
 - `Average` - The average of all the values
 - `All` - Whether all the values evaluate to true
 - `Any` - Whether any of the values evaluate to true
 - `Implode` - Concatenates the values seperated by the supplied delimiter

**Other**
 - `AsArray` - The values as an array

ICollection
===========

The `ICollection` interface represents a queryable range of values that are also mutable, they can be manipulated and altered using the additional methods:

 - `Apply` - Walks the values with the supplied function
 - `AddRange` - Adds a range of values to the collection
 - `RemoveRange` - Removes a range of values from the collection
 - `RemoveWhere` - Removes the values according to the supplied predicate function
 - `Clear` - Removes all the values from the collection.


<a name="pinq-examples"></a>Some query examples
===============================================

The below examples shows the query functionality using the built-in `Traversable` class. This is an implementaion of `ITraversable` that uses iterators to perform the query in-memory.

```php
use Pinq\ITraversable, Pinq\Traversable;

//All Even values
$Numbers = Traversable::From(range(1, 10))->Where(function ($I) { return $I % 2 === 0; });

//Values multiplied by 10
$Numbers = Traversable::From(range(1, 5))->Select(function ($I) { return $I * 10; });

//Average string length
$Strings = Traversable::From(['foo', 'bar', 'crocodile'])->Average('strlen');

//Order by ascending first then by descending third charater
$Strings = Traversable::From(['foo', 'bar', 'baz'])
        ->OrderByAscending(function ($I) { return $I[0]; })
        ->ThenByDescending(function ($I) { return $I[2]; });

//A complex example
$Data = Traversable::From(range(1,100))
        ->Where(function ($I) { return $I % 2 === 0; }) //Only even values
        ->OrderByDescending(function ($I) { return $I; }) //Order from largest to smallest
        ->GroupBy(function ($I) { return $I % 7; }) //Put into seven groups
        ->Where(function (ITraversable $I) { return $I->Count() % 2 === 0; }) //Only groups with an even amount of values
        ->Select(function (ITraversable $Numbers) {
            return [
                'First' => $Numbers->First(),
                'Average' => $Numbers->Average(),
                'Count' => $Numbers->Count(),
                'Numbers' => $Numbers->AsArray(),
            ];
        });
```

Limitations
===========
 - Within the query, one should not use control structures such as `if, switch, goto, while, foreach,...`, these are not classified as valid query expressions and cannot be used with external data sources.

<a name="pinq-external-source"></a>Querying on external data sources
====================================================================
The `IQueryable` and `IRepository` interfaces are respective to `ITraversable` and `ICollection`. But the data source for these is not limited to an array or iterator, they are able to use any implementation of `IQueryProvider`/`IRepositoryProvider` respectively, one could query a database, XML, DOM, CSV, the possibilities are endless.

Implementing an IQueryProvider
==============================
In this exercise to understand the `IQueryProvider` and how it is to be implemented, we will create a basic query provider for a constant array of numbers `[1,2,3,4,5,6,7,8,9,10]` (This is pointless as there is `Traversable` for a PHP array but this should help to illustrate the concept):

The goal of this will be making a query provider capable of executing:
```php
$MyQueryableArray
        ->Where(function ($Number)  { return $Number > 5; })
        ->Select(function ($Number)  { return $Number * 10; })
        ->Sum();
```

To implement a `IQueryProvider` one must understand the how the query will be represented, a query is represented in two parts:
 - **`IScope`** - This contains many `ISegment`, each segment represents one or more methods calls from the `IQueryable` implementation. For example the `->Where(...)` would become a `Filter` segment and the `->Select(...)` would becom a `Select` segment. 
 - **`IRequest`** - This represents the actual data requested, in this case `->Sum()`  so a `Sum` request, but it could be the underlying values (`AsArray()`), another aggregate (`Count()`, ...) etc.

There is also the `FunctionExpressionTree`, functions in the query will be parsed into this class, this contains details of the parameters and an expression tree representing the body of the function.

**Steps**

 - For the first step we need the a class that will evaluate the expression tree of a given function against the array of values, this will extend the `ExpressionVisitor` class to traverse the expression tree:

```php
use \Pinq\Expressions as O;
use \Pinq\FunctionExpressionTree;

/**
 * To keep it consice we will only implement the bare minimum needed to evaluate the requirements.
 * This is capable of evaluating a single binary operation involving > or *
 */
class ExpressionTreeEvaluator extends O\ExpressionVisitor
{
    private $Array;
    private $MappedReturnedValues;
    
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }
    
    /**
     * Nice static method to easily evaluate the return expression against the array
     */
    public static function EvaluateReturn(FunctionExpressionTree $ExpressionTree, array $Array) {
        $Evaluator = new self($Array);
        
        $Evaluator->Walk($ExpressionTree->GetFirstResolvedReturnValueExpression());
        
        return $Evaluator->MappedReturnedValues;
    }
    
    protected function VisitBinaryOperation(O\BinaryOperationExpression $Expression)
    {
        //Ignore the left operand, assume it is the value parameter
        
        $RightExpression = $Expression->GetRightOperandExpression();
        
        if(!($RightExpression instanceof O\ValueExpression)) {
            throw new \Exception('I need a constant value on the right side of the query expression');
        }
        
        $RightValue = $RightExpression->GetValue();
        
        switch ($Expression->GetOperator()) {
            case O\Operators\Binary::GreaterThan:
                $this->MappedReturnedValues = array_map(function ($I) use ($RightValue) { return $I > $RightValue; }, $this->Array);
                break;
            
            case O\Operators\Binary::Multiplication:
                $this->MappedReturnedValues = array_map(function ($I) use ($RightValue) { return $I * $RightValue; }, $this->Array);
                break;

            default:
                throw new \Exception('I cannot do this operation: ' . $Expression->GetOperator());
        }
    }
}
```

 - For the second step we need the a class that will evaluate the query scope of the supplied array, this extends the `SegmentVisitor` and will evaluate the `->Where(...)->Select(...)` part of the query:

```php
use \Pinq\Queries\Segments;

/**
 * This is also the bare minimum.
 * Evaluating only 'Where' and 'Select' and ignoring the rest
 */
class QueryScopeEvaluator extends Segments\SegmentVisitor
{
    private $Array;
    
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }
    
    public function GetScopedArray()
    {
        return $this->Array;
    }
    
    public function VisitFilter(Segments\Filter $Query)
    {
        $MappedResults = ExpressionTreeEvaluator::EvaluateReturn($Query->GetFunctionExpressionTree(), $this->Array);
        
        foreach($MappedResults as $Key => $Value) {
            //Remove any values that returned false from the function
            if(!$Value) {
                unset($this->Array[$Key]);
            }
        }
    }
    
    public function VisitSelect(Segments\Select $Query)
    {
        $MappedResults = ExpressionTreeEvaluator::EvaluateReturn($Query->GetFunctionExpressionTree(), $this->Array);
        
        //Set the array to the projections from the function
        $this->Array = $MappedResults;
    }
}
````

 - Now to evaluate the `->Sum()` aggregate value, extending the `RequestVisitor` class, this is responsible for evaluating all the aggregates and retrieving the values:

```php

use \Pinq\Queries\Requests;
use \Pinq\FunctionExpressionTree;

/**
 * This is also the bare minimum, evaluating only 'Sum'
 */
class RequestEvaluator extends Requests\RequestVisitor
{
    private $Array;
    
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }
    
    public function VisitSum(Requests\Sum $Request)
    {
        if(count($this->Array) === 0) {
            return null;
        }
        
        //BONUS: if average was called with a projection function we can evaluate that to
        $ProjectedValues = $this->EvaluateProjection($Request);
        
        $Sum = 0;
        foreach($ProjectedValues as $Value) {
            $Sum += $Value;
        }
        
        return $Sum;
    }
    
    private function EvaluateProjection(Requests\ProjectionRequest $Request) 
    {
        if($Request->HasFunctionExpressionTree()) {
            return ExpressionTreeEvaluator::EvaluateReturn($Request->GetFunctionExpressionTree(), $this->Array);
        }
        else {
            return $this->Array;
        }
    }
}
```

- Bringing it all together and implementing the `IQueryProvider` by extending `QueryProvider` which implements basic boiler plate for the query provider:

```php
use \Pinq\Providers;
use \Pinq\Queries;

class ArrayQueryProvider extends Providers\QueryProvider 
{
    private $Array = [1,2,3,4,6,7,8,9,10];
    
    protected function LoadRequestEvaluatorVisitor(Queries\IScope $Scope)
    {
        //Evaluate the query scope: ->Where(...)->Select(...)
        $ScopedEvaluator = new QueryScopeEvaluator($this->Array);
        $ScopedEvaluator->Walk($Scope);
        $ScopedArray = $ScopedEvaluator->GetScopedArray();
        
        //Return the request evaluator, it will be called and evaluate the ->Sum() query
        return new RequestEvaluator($ScopedArray);
    }
}
```

- If we wanted to we could wrap this in a nice subclass of `Queryable` and automatically pass the appropriate query provider:

```php
class ArrayQueryable extends \Pinq\Queryable
{
    public function __construct()
    {
        parent::__construct(new ArrayQueryProvider());
    }
}
```

Finally, we can see the action:

```php
$MyQueryableArray = new ArrayQueryable();

echo $MyQueryableArray
        ->Where(function ($Number)  { return $Number > 5; })
        ->Select(function ($Number)  { return $Number * 10; })
        ->Sum();
//Echos: 400
```

This is an extremely basic, useless and naive implementation of the query provider, but nevertheless it does exactly as we wanted. 

Hopefully, this helps to illustrate the capabilities of Pinq and its built-in expression language. Its ability and to provide a seamless integration with PHP and external data sources.
