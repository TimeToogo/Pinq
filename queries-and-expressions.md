---
layout: default
title:  Queries and Expressions
---

The anatomy of query
====================

Much of the magic of PINQ is its ability to offer a seamless integration with typical PHP code to perform queries on
external data sources, hence *PHP integrated query*.
To offer this functionality, one must [implement the `IQueryProvider`](/query-provider.html). To do this, one must
understand the process of how queries are parsed and represented from plain PHP code.

**A typical query may be as follows:**
{% highlight php startinline %}
$peopleTable
        ->where(function ($row) use ($maxAge) { return $row['age'] <= $maxAge; })
        ->orderByAscending(function ($row) { return $row['firstName']; })
        ->thenByAscending(function ($row) { return $row['lastName']; })
        ->take(50)
        ->asArray();
{% endhighlight %}

**Here is the terminology used in the library:**

 - **Query**: A chain of method calls against an `IQueryable` instance.
 - **Request query**: A type of query which expects a returned value.
 - **Request**: The part of the request query which specifies the result.
 - **Operation query**: A type of query which modifies the underlying elements.
 - **Operation**: The part of the operation query which specifies how to modify the values.
 - **Scope**: The part of the query which determines which elements and the order to perform the request or operations.
 - **Source**: The representation of the underlying elements.
 - **Segment**: An individual part of the scope.
 - **Function**: A function, method or closure within the query.
 - **Expression**: A piece of PHP code within a function.
 - **Parameter**: A value that is used within but external to the query.

**According to these definitions we can dissect the above query into the following:**

 - The following method calls can defined as the scope of the query: {% highlight php startinline %}
$peopleTable
        ->where(function ($row) { return $row['age'] <= 50; })
        ->orderByAscending(function ($row) { return $row['firstName']; })
        ->thenByAscending(function ($row) { return $row['lastName']; })
        ->take(50)
{% endhighlight %}
 - `$peopleTable`: Is source of the query, the instance of `IQueryable` that contains the necessary data about the source of the query.
 - The method calls can be further dissected into individual segments:
    - A segment which filters the elements: {% highlight php startinline %}
->where(function ($row) use ($maxAge) { return $row['age'] <= $maxAge; })
{% endhighlight %}
    - A segment which sorts the elements: {% highlight php startinline %}
->orderByAscending(function ($row) { return $row['firstName']; })
->thenByAscending(function ($row) { return $row['lastName']; })
{% endhighlight %}
    - A segment which takes a range of the elements. {% highlight php startinline %}
    ->take(50)
{% endhighlight %}
 - `->asArray()`: Defines the request, it specifies to return the elements as an array.
 - There are multiple functions within this query:
    - An argument to the `where` segment: {% highlight php startinline %}
->where(function ($row) use ($maxAge) { return $row['age'] <= $maxAge; })
{% endhighlight %}
    - Also as arguments to the `orderBy..thenBy` segment.
 - Each function contains an array of expressions defining the function parameters: `$row`, (not query parameters) and body statements.
 - There are multiple parameters in this query
    - The argument to `take`: 50
    - The used variable in the first closure: `$maxAge`
    - Possibly a `$this` variable for each closure if they were created in an instance of a class.

**We can define the following relationships:**

 - A **request query** has a request, a scope and a collection of all the parameters.
 - An **operation query** has an operation, a scope and a collection of all the parameters.
 - A **scope** has a source and a collection of segments.
 - A **segment, operation or request** may contain functions or parameters.
 - A **function** contains expressions and a collection of parameters.

Representing queries as objects
===============================
Using the above terminology, PINQ provides the ability to represent any query as a structured object.

**The following interfaces and implementations under the `Pinq\Queries` namespace are used:**

 - `IQuery` - base interface of a structured query object.
 - `IRequestQuery` - interface of a request query.
 - `IRequest` - interface of a request.
 - `IOperationQuery` - interface of a operation query.
 - `IOperation` - interface of a operation.
 - `IScope` - interface of a scope.
 - `ISourceInfo` - interface of a source.
 - `ISegment` - interface of a segment.
 - `Functions\Base` - base class of a function structure.
 - `IParameterRegistry` - interface of a collection of required parameters.
 - `IResolvedParameterRegistry` - interface of a collection of resolved parameter values.

As there are many types of **segments, requests, operations and functions** as defined in the `IQueryable`/`IRepository` interface,
**for each of their interfaces, concrete types are defined under the following:**

 - `Queries\Segments` - contains all the types of segments.
 - `Queries\Requests` - contains all the types of requests.
 - `Queries\Operations` - contains all the types of operations.
 - `Queries\Functions` - contains all the types of functions.

Expressions are implemented under the `Pinq\Expressions` namespace with a base `Expression` class.

You can explore all the implementations in the [API documentation](/docs)

**There are two stages in the process of parsing the PHP code into a query object:**

 - Firstly, the `IQueryable` implementation constructs an expression tree containing the method calls and arguments.
 - This is sent to the `IQueryProvider` which uses a configured query builder to interpret the expression tree, creating
   a unique hash for the query and retrieving all the parameter values.
 - Then the expression tree is parsed into a structured query object, if it has not already been cached.
   In this process, functions in the query are be parsed into an expression tree. This is expensive and hence query objects
   are cached using the configured cache adapter.
 - The retrieved parameter values are matched against the query parameters.

Both the query object and resolved parameter values are interpreted to retrieve the underlying parameter values.

Representing functions as objects
=================================

Within query objects, functions are represented as their own class under the `Queries\Functions` namespace.
Each function class contains specific API for interpreting that type of function:

 - **`ElementProjection`** - A function which takes a value and key and returns a value: {% highlight php startinline %}function ($value, $key) { return ... }{% endhighlight %}
 - **`ElementMutator`** -  A function which takes a value (possibly by-ref) and key and mutates the value: {% highlight php startinline %}function ([&]$value, $key) { ... }{% endhighlight %}
 - **`ConnectorProjection`** - A function which takes a two pairs of values and keys returns a value: {% highlight php startinline %}function ($outerValue, $innerValue, $outerKey, $innerKey) { return ... }{% endhighlight %}
 - **`ConnectorMutator`** - A function which takes a two pairs of values (the first possibly by-ref)  and keys mutates the first value: {% highlight php startinline %}function ([&]$outerValue, $innerValue, $outerKey, $innerKey) { ... }{% endhighlight %}
 - **`Aggregator`** - A function which takes the aggregate (initially the first value) and a value and returns a new aggregate: {% highlight php startinline %}function ($aggregate, $value) { return ... }{% endhighlight %}

To the function body is represented an array of expression trees. An expression tree represents the structure of a piece of code.

For example `5 + 3 - 2` can be represented in the following structure:

```
          BinaryOperation
          |           |           |
        Left   Operator   Right
         /            |             \
        5            +          BinaryOperation
                                   |           |           |
                                 Left   Operator  Right
                                 /             |              \
                                3             -                2
```

Interpreting queries
====================
PINQ does much of the above, parsing a chain of method calls into a structured query object.
But to implement the query provider, one must be able to interpret this query object to perform the desired query.

As mentioned there are many types of segments, requests, operations and expressions. To aide in interpreting the various types,
the [visitor pattern](http://en.wikipedia.org/wiki/Visitor_pattern) is used. Providing a class with a method for each concrete
type, they can be extended interpret each type as required.

**The following visitors are provided:**

 - `Queries\Segments\SegmentVisitor` - for interpreting each type of segment.
 - `Queries\Requests\RequestVisitor` - for interpreting each type of request.
 - `Queries\Operations\OperationVisitor` - for interpreting each type of operation.
 - `Expressions\ExpressionVisitor` - for interpreting an expression trees.

Click here for a more detailed guide on [implementing the `IQueryProvider`](/query-provider.html)

Modifying an expression tree
============================

To modify an expression tree, `ExpressionWalker`, This is designed to traverse an entire expression tree,
contains a set of overridable methods, one for each type of expression. These can be
implemented to handle and `update` any type of expression as desired.

**Example:**

This expression walker will replace every variable's name with `'foo'`

{% highlight php startinline %}
use Pinq\Expressions as O;

class VariableNameReplacer extends O\ExpressionWalker
{
    public function walkVariable(O\VariableExpression $expression)
    {
        return $expression->update(O\Expression::value('foo'));
    }
}
{% endhighlight %}

The `DynamicExpressionWalker` is a lightweight version of the expression walker that provides the ability to update
expression trees without the overhead of defining an entire class.
The equivalent of the above is:

{% highlight php startinline %}
use Pinq\Expressions as O;

$walker = new O\DynamicExpressionWalker ([
    O\VariableExpression::getType() => function (O\VariableExpression $expression, ExpressionWalker $self)
    {
        return $expression->update(O\Expression::value('foo'));
    }
]);
{% endhighlight %}

Implemented Segments
====================

The segment classes are implemented under the `Queries\Segments` namespace:

 - **`Filter`**
   - Only use elements for which the projection returns a value which are truthy.
   - `->where(<element projection>)`

 - **`OrderBy`**
   - Order the elements according to the defined element projection(s) and direction(s).
   - `->orderBy(<element projection>, <direction parameter>)`
   - `->orderByAscending(<element projection>)`
   - `->orderByDescending(<element projection>)`
   - `->thenBy(<element projection>, <direction parameter>)`
   - `->thenByAscending(<element projection>)`
   - `->thenByDescending(<element projection>)`

 - **`GroupBy`**
   - Group the elements according to the element projection into instances of `ITraversable`.
   - `->groupBy(<element projection>)`

 - **`Range`**
   - Only use elements from in the supplied range.
   - `->take(<amount parameter>)`
   - `->skip(<start parameter>)`
   - `->slice(<start parameter>, <amount parameter>)`

 - **`IndexBy`**
   - For each element set the key as the result of the element projection.
   - `->indexBy(<element projection>)`

 - **`Keys`**
   - Use the keys of each element.
   - `->keys()`

 - **`Reindex`**
   - Reset the keys of the elements to incrementing integers from 0 based on the element's order.
   - `->reindex()`

 - **`Select`**
   - Use the return value of the element projection as the value.
   - `->select(<element projection>)`

 - **`SelectMany`**
   - Use the return value of the element projection as the value and flatten the results.
   - `->selectMany(<element projection>)`

 - **`Unique`**
   - Only use unique elements according to their values.
   - `->unique()`

 - **`Operation`**
   - A set/multiset operation to perform with another sequence of elements.
   - `->union(<sequence source>)`
   - `->intersect(<sequence source>)`
   - `->difference(<sequence source>)`
   - `->append(<sequence source>)`
   - `->whereIn(<sequence source>)`
   - `->except(<sequence source>)`

 - **`Join`**
   - Correlate the elements with another sequence of elements and use the return value from the connector projection.
   - `<join options>->to(<connector projection>)`


Implemented Requests
====================

The request classes are implemented under the `Queries\Requests` namespace:

 - **`Aggregate`**
   - Get the aggregate of the element values using the supplied aggregator function.
   - `{scope}->aggregate(<aggregator>)`

 - **`All`**
   - Get whether all of the elements values or projected values if an element projection is supplied are truthy.
   - `{scope}->all([element projection])`

 - **`Any`**
   - Get whether any of the elements values or projected values if an element projection is supplied is truthy.
   - `{scope}->any([element projection])`

 - **`Average`**
   - Get the average value of the elements or projected values if an element projection is supplied.
   - `{scope}->any([element projection])`

 - **`Contains`**
   - Get whether the supplied value parameter is contained within the scope.
   - `{scope}->contains(<value parameter>)`

 - **`Count`**
   - Get the amount of elements in the scope.
   - `{scope}->count()`

 - **`First`**
   - Get the first element value in the scope.
   - `{scope}->first()`

 - **`GetIndex`**
   - Get the value associated with the supplied index parameter.
   - `{scope}->offsetGet(<key parameter>)`
   - `{scope}[<key parameter>]`

 - **`Implode`**
   - Get a delimited string of the elements values or projected values if an element projection is supplied.
   - `{scope}->implode(<delimiter parameter>, [element projection])`

 - **`IsEmpty`**
   - Get whether there are no elements in the scope.
   - `{scope}->isEmpty()`

 - **`IssetIndex`**
   - Get whether there is a value associated with the supplied index parameter.
   - `{scope}->offsetExists(<key parameter>)`
   - `isset({scope}[<key parameter>])`

 - **`Last`**
   - Get the last element value in the scope.
   - `{scope}->last()`

 - **`Maximum`**
   - Get the maximum value of the elements or projected values if an element projection is supplied.
   - `{scope}->maximum([element projection])`

 - **`Minimum`**
   - Get the maximum value of the elements or projected values if an element projection is supplied.
   - `{scope}->minimum([element projection])`

 - **`Sum`**
   - Get the sum of the values of the elements or projected values if an element projection is supplied.
   - `{scope}->sum([element projection])`

 - **`Values`**
   - Get the underlying elements.
   - `{scope}->asArray()` - as an array.
   - `{scope}->getIterator()` - as an array compatible iterator (with scalar keys).
   - `{scope}->getTrueIterator()` - as an iterator.
   - `{scope}->asTraversable()` - as an `ITraversable` instance.
   - `{scope}->asCollection()` - as an `ICollection` instance.


Implemented Operations
======================

The operation classes are implemented under the `Queries\Operations` namespace:

 - **`AddValues`**
   - Adds a sequence of elements to the underlying elements.
   - `{scope}->addRange(<sequence source>)`

 - **`Apply`**
   - Update the underlying elements within the scope with the supplied element mutator.
   - `{scope}->apply(<element mutator>)`

 - **`Clear`**
   - Remove all the underlying elements within the scope.
   - `{scope}->clear()`

 - **`JoinApply`**
  - Correlate the elements with another sequence of elements and update the outer elements with the supplied connector mutator.
  - `<join options>->to(<connector mutator>)`

 - **`RemoveValues`**
   - Removes a sequence of elements within the scope from the underlying elements.
   - `{scope}->removeRange(<sequence source>)`

 - **`RemoveWhere`**
    - Removes any underlying elements for which the projection returns a value which are truthy.
    - `{scope}->where(<element projection>)`

 - **`SetIndex`**
    - Sets the value associated with the supplied index parameter to the supplied value parameter.
    - `{scope}->offsetSet(<key parameter>, <value parameter>)`
    - `{scope}[<key parameter>] =  <value parameter>`

 - **`UnsetIndex`**
    - Removes the value associated with the supplied index parameter.
    - `{scope}->offsetUnset(<key parameter>)`
    - `unset({scope}[<key parameter>])`


Sequence source
===============

There are multiple places within a query where a sequence from elsewhere is used as a part of the query.

For example when performing an join or adding or removing values.

A sequence source is represented by the `Queries\Common\ISource` interface.
There are two types on implemented sequence sources under the `Queries\Common\Source` namespace:

 - `ArrayOrIterator` - A parameter which results in an array or iterator. {% highlight php startinline %}
->join([1, 2, 3, 4, 5])
->removeRange(range('A', 'Z'))
{% endhighlight %}
 - `QueryScope` - A separate scope from another query:{% highlight php startinline %}
->join($source->select(function ($i) { return $i . '!'; })->take(10))
->removeRange($source->where(function ($i) { return $i > 5; }))
{% endhighlight %}

Join options
============

Join options can also used from multiple places within the query.

The terms **outer elements and inner elements** are used. The outer elements are the elements original elements whereas the
inner elements are the elements which are being joined to:{% highlight php startinline %}
<outer elements>->join(<inner elements>)
{% endhighlight %}

**The `Queries\Common\Join\Options` class consists of:**

 - The type of join:
    - `->join(...)` - Every outer element will be filtered against the inner elements.
    - `->groupJoin(...)` - For every outer element, the filtered inner elements are grouped into an instance of `ITraversable`.
 - The join filter, there are two types of join filters under the `Queries\Common\Join\Filter` namespace:
    - `Custom` - A function which filters on both the outer and inner elements `->on(<connector projection>)`
    - `Equality`  - A function for the outer elements and one for the inner elements.
       They are filtered according to eqaulity between them but `null` is ignored.
       `->onEquality(<outer element projection>, <inner element projection>)`
 - A default element may be set which will be used as an inner element if there are no filtered inner elements for any outer element.
    `->withDefault(<value parameter>, <key parameter>)`


Implemented Expressions:
========================

To represent PHP code the following expression classes under the `Expressions` namespace are used:

 - **`ArgumentExpression`**
    - `func($arg)`
    - `func(...$arg)`

 - **`ArrayExpression`**
    - `[1, 4, 5 => 4]`
    - `array('foo', 'bar', 'baz')`

 - **`ArrayItemExpression`**
    - `5 => 4`

 - **`AssignmentExpression`**
    - `$i = 5`
    - `$i += 5`
    - `$i /= 5`...

 - **`BinaryOperationExpression`** 
    - `3 + 5`
    - `3 - 5`
    - `'foo' . 'bar'`...

 - **`CastExpression`** 
    - `(int)$i`
    - `(string)$i`...

 - **`ClosureExpression`** 
    - `function ($i) {...}`

 - **`ConstantExpression`**
    - `SOME_CONSTANT`

 - **`ClassConstantExpression`**
    - `SomeClass::SOME_CONSTANT`

 - **`EmptyExpression`** 
    - `empty($i)`

 - **`FieldExpression`** 
    - `$i->field`

 - **`FunctionCallExpression`** 
    - `strlen($i)`

 - **`IndexExpression`** 
    - `$i[3]`

 - **`InvocationExpression`** 
    - `$i()`

 - **`IssetExpression`** 
    - `isset($i)`

 - **`MethodCallExpression`** 
    - `$i->method()`

 - **`NewExpression`** 
    - `new \stdClass()`
    - `new \DateTime()`

 - **`ParameterExpression`**
    - `function ($i) ...`
    - `function (\stdClass &$i = null) ...`

 - **`ReturnExpression`** 
    - `return $i`

 - **`StaticFieldExpression`**
    - `Object::$field`

 - **`StaticMethodCallExpression`**
    - `Object::method()`

 - **`TernaryExpression`** 
    - `$i === true ? 1 : -1`

 - **`ThrowExpression`** 
    - `throw $i`

 - **`UnaryOperationExpression`** 
    - `-$i`
    - `$i++`
    - `!$i`
    - `--$i`
    - `~$i`...

 - **`UnsetExpression`**
    - `unset($i, $v)`

 - **`ValueExpression`** 
    - `4`
    - `5.5`
    - `'test'`
    - `null`
    - `true`...

 - **`VariableExpression`** 
    - `$i`

Note that control structures such as `foreach`, `for`, `if` are not implemented
an hence are not allowed within queries.