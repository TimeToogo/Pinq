---
layout: default
title:  Query API
---
ITraversable
============

The `ITraversable` interface represents a range of values that are able to be queried upon.
This is base interface for all query objects, it supports the following methods:

**Queries**

 - `where` - Filters the values according to the supplied predicate
 - `orderBy/orderByAscending/orderByDescending->thenBy/thenByAscending/thenByDescending...` - 
    Orders the values according the supplied order functions and direction(s)
 - `skip` - Skip the supplied amount of values
 - `take` - Limits the amount of values by the supplied amount
 - `slice` - Retrieve a slice of values according to the specified offset and amount
 - `indexBy` - Index the values according to the supplied mapping function
 - `groupBy->andBy...` - Groups the values according the supplied grouping function(s)
 - `join->on/onEquality->to` - Matches the values with the supplied values according to the supplied filter then 
   then maps the results into as according to the supplied function.
 - `groupJoin->on/onEquality->to` - Matches the values with the supplied values according to the supplied filter, 
   groups the results and then maps into as according to the supplied function.
 - `unique` - Only return unique values
 - `select` - Map the values according to the supplied function
 - `selectMany` - Map the values according to the supplied function and merge the results
 - `first` - Returns the first value or null if empty
 - `last` - Returns the last value or null if empty
 - `contains` - Returns if the supplied value is present in the values
 - `offsetGet` - Returns a value at the supplied index
 - `offsetExists` - Whether a value exists for the supplied index


**Set/List Operations**

 - `append` - All values present in either the original or supplied values
 - `whereIn` - All values present in both the original and supplied values
 - `except` - All values present in the original but not in the supplied values
 - `union` - Unique values present in either the original or supplied values
 - `intersect` - Unique values present in both the original and supplied values
 - `difference` - Unique values present in the original but not in the supplied values

**Aggregates**

 - `count` - The amount of values
 - `exists` - Whether there are any values
 - `aggregate` - Aggregates the values according to the supplied
 - `maximum` - The maximum value
 - `minimum` - The minimum value
 - `sum` - The sum of all the values
 - `average` - The average of all the values
 - `all` - Whether all the values evaluate to true
 - `any` - Whether any of the values evaluate to true
 - `implode` - Concatenates the values seperated by the supplied delimiter

**Other**

 - `asArray` - The values as an array

ICollection
===========

The `ICollection` interface represents a queryable range of values that are also mutable, 
they can be manipulated and altered using the additional methods:

 - `apply` - Walks the values with the supplied function
 - `addRange` - Adds a range of values to the collection
 - `removeRange` - Removes a range of values from the collection
 - `removeWhere` - Removes the values according to the supplied predicate function
 - `clear` - Removes all the values from the collection.
 - `offsetSet` - Sets a value to supplied index
 - `offsetUnset` - Removes any value at the supplied index

IQueryable 
==========

The `IQueryable` interface represents is another version of the `ITraversable` interface.
This provides the same API of the `ITraversable` but through the use of a `IQueryProvider`,
it supports querying external data sources.

IRepository 
===========

The `IRepository` interface represents is another version of the `ICollection` interface.
This provides the same API of the `ICollection` but through the use of a `IRepositoryProvider`,
it supports querying and mutating external data sources.


Limitations
===========

 - Within a query, one should not use control structures such as `if, switch, goto, while, foreach,...`, 
   these are not classified as valid query expressions and cannot be used with external data sources.

 - One should not define multiple closures on the same line. An exception will be thrown for the following:
   {% highlight php startinline %}
   $Queryable->where(function ($i) { return $i > 50; })->where(function ($i) { return $i !== 70; });{% endhighlight %}


Standard Classes
================

Along side the [API](api.html), Pinq comes the set of standard implementations for each of
the interfaces. If you need to add and custom functionality to the Pinq API, you should extend
these classes as they contain the [correct and tested implementation](details.html) for the 
standard API.

The following classes are provided:

 - `Traversable implements ITraversable`

 - `Collection implements ICollection`

 - `Queryable implements IQueryable`

 - `Repository implements IRepository`
