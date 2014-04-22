---
layout: default
title:  Query API
---
ITraversable
============

The `ITraversable` interface represents a range of values that are able to be queried upon.
This is base interface for all query objects, it supports the following methods:

**Queries**

 - `Where` - Filters the values according to the supplied predicate
 - `OrderBy/OrderByAscending/OrderByDescending->ThenBy/ThenByAscending/ThenByDescending...` - 
    Orders the values according the supplied order functions and direction(s)
 - `Skip` - Skip the supplied amount of values
 - `Take` - Limits the amount of values by the supplied amount
 - `Slice` - Retrieve a slice of values according to the specified offset and amount
 - `IndexBy` - Index the values according to the supplied mapping function
 - `GroupBy->AndBy...` - Groups the values according the supplied grouping function(s)
 - `Join->On/OnEquality->To` - Matches the values with the supplied values according to the supplied filter then 
   then maps the results into as according to the supplied function.
 - `GroupJoin->On/OnEquality->To` - Matches the values with the supplied values according to the supplied filter, 
   groups the results and then maps into as according to the supplied function.
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

The `ICollection` interface represents a queryable range of values that are also mutable, 
they can be manipulated and altered using the additional methods:

 - `Apply` - Walks the values with the supplied function
 - `AddRange` - Adds a range of values to the collection
 - `RemoveRange` - Removes a range of values from the collection
 - `RemoveWhere` - Removes the values according to the supplied predicate function
 - `Clear` - Removes all the values from the collection.

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
