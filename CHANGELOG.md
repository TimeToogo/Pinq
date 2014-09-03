dev-master
==========
 - Query functions are passed each value with their associated key as the second parameter while
   maintaining support for single parameter internal functions.
 - Proper support for non scalar keys:
    - Non scalar keys will be automatically converted to integers when foreach'd or converted to an array.
    - Added `ITraversable::iterate` to iterate all unaltered values and keys
    - Added `ITraversable::keys` select the keys and `ITraversable::reindex` to reindex the values by their 0-based position
    - Added `ITraversable::getTrueIterator` to get the iterator for all values and unaltered keys.
 - Refactored iterator structure:
    - Abstracted iterator implementations under `Iterators\IIteratorScheme`.
    - Now supports generators for performance improvement and reduced memory usage for >= PHP 5.5.0
    - Will fall back to iterators for <= PHP 5.5.0
        - Native iterators have also been improved with regards to performance.
 - Implemented new `ITraversable` source semantics:
    - A source `ITraversable` is the instance containing the original underlying elements.
    - Added `ITraversable::isSource`, returns whether the instance is the source `ITraversable`.
    
        ```php
        $elements = Traversable::from(range(1, 100));
        $elements->isSource(); //true
        $someElements = $elements->where(function ($i) { return $i > 50; });
        $someElements->isSource(); //false
        ```

    - Added `ITraversable::getSource`, returns the source `ITraversable` or itself if it is the source.
    - Removed unnecessary caching in `Traversable` queries.
        - `Traversable` can be used with nondeterministic/mutable sources and query parameters.
        - `Traversable` classes can longer be serialized when queried with closures.
        - Because of this combined with covariant return types, `ICollection` has new and improved mutable query API:
        
        ```php
        $collection = Collection::from(range(1, 10));
        $collection
                ->where(function ($i) { return $i >= 5; })
                ->apply(function (&$i) { $i *= 10; });
        
        $collection->asArray();//[1, 2, 3, 4, 50, 60, 70, 80, 90, 100]

        $collection
                /* ... */
                ->clear();
        //Is equivalent to:
        $collection
                ->removeRange(/* ... */);
        ```
 - Removed `ITraversable::asQueryable`, `ITraversable::asRepository`, updated interfaces annotations
   with covariant return types. `ITraversable`, `ICollection`, `IQueryable`, `IRepository` should all return
   their respective types for each query method.
 - Moved/Implemented Necessary interfaces (`IOrdered*`, `IJoiningOn*`, `IJoiningTo*`) with covariant 
   return types under `Interfaces` namespace.
 - Removed `IGroupedTraversable`, use `ITraversable::groupBy` returning an array instead.
 - `ITraversable::groupBy` implicitly indexes each group by the group key.
 - Changed `ITraversable::exists` in favour of `ITraversable::isEmpty`.
 - `Traversable`/`Collection` are now extendable.
 - `IJoiningOnTraversable::onEquality` will not match `null`s as equal as in C#.
 - Implemented optional default value for `ITraversable::join`/`ITraversable::groupJoin`:
    - The following join query:
    
    ```php
    Traversable::from(range(1, 6))
        ->join(range(1, 20))
        ->on(function ($outer, $inner) { return $outer % 2 === 0 && $outer * 2 === $inner; })
        ->withDefault('<Odd>')
        ->to(function ($outer, $inner) { 
            return $outer . ':' . $inner;
        });
    ```
    Will produce: `['1:<Odd>', '2:4', '3:<Odd>', '4:8', '5:<Odd>', '6:12']`
 - Added `join(...)->apply(...)` operation for `ICollection`/`IRepository`
 - Added `ICollection::remove` to remove all occurrences of the supplied value from the collection.
 - Made order by `Direction` constants interchangeable with native `SORT_ASC`/`SORT_DESC`.
 - Shorten expression getter names by removing redundant `...Expression`.
 - Restructured and improved function parsing
    - New function reflection API
    - Correctly handle resolving magic constants (`__DIR__`...) and scopes (`self::`...).
    - Largely improved signature matching using all reflection data to resolve to the correct function.
      Functions now have to be defined on the same line with identical signatures to cause an ambiguity.
    - Fixed fully qualified namespace detection in AST parsing.
    - Upgraded to `nikic/php-parser` V1.0.0beta2 with compatibility for 5.6 syntax features.
 - Improved query representations (`Queries` namespace)
    - New builder API (under `Builders`) to build query objects from expression trees.
      `Queryable`/`Repository` now only construct the query expression tree.
      These classes parse the expression tree into the equivalent query structure.
    - Query parameters are now externalized from the query object. Under a `IParameterRegistry` instance.
    - New common `ISource` interface for a another sequence inside a query: `->intersect(...)`
    - Removed `FunctionExpressionTree` in favour of dedicated function types (under `Queries\Functions` namespace)
      for all types of functions in a query:
        - `ElementProjection`: `->select(function ($value, $key) { return ... })`
        - `ElementMutator`: `->apply(function (&$value, $key) { ... })`
        - `ConnectorProjection`: `->join(...)->to(function ($outerValue, $innerValue, $outerKey, $innerKey) { return ... })`
        - `ConnectorMutator`: `->join(...)->apply(function (&$outerValue, $innerValue, $outerKey, $innerKey) { ... })`
        - `Aggregator`: `->aggregate(function ($aggregate, $value) { return ... })`
    - New `ISourceInfo` to store source information of a `IQueryable`.
    - Refactored `Segments\OrderBy` query segment by representing each
      function and direction as an `Queries\Segments\OrderFunction` class.
    - Renamed `Segments\Operation::getTraversable` to `getValues`
    - Refactored `Join` query segments / operations.
       - Refactored inheritance to composition, extracted join filtering to interface `Queries\Common\Join\IFilter`.
       - Created class containing common join options: `Queries\Common\Join\Options`.
    - Extracted interfaces from `Request`/`Operation`/`Segment` visitor classes.
 - Removed obsolete query providers (`Loadable`, `Caching`) in favour of a new integrated helper `Providers\Utilities\IQueryResultCollection`
 - Implemented new DSL query provider under `Providers\DSL`.
 - New structure of query providers
    - `RepositoryProvider` decorates the `QueryProvider`
    - New configuration classes (under `Providers\Configuration` namespace)
    - Integrated with `Caching\IQueryCache` and `Queries\Builders\*`.
 - New expression classes: `UnsetExpression`, `StaticFieldExpression`, `ConstantExpression`, `ClassConstantExpression`
 - Refactored `ArrayExpression` by creating `ArrayItemExpression` representing each element.
 - Refactored `ClosureExpression` by creating `ClosureUsedVariableExpression` representing each used variable and supports references.
 - Updated expression simplification to use compilation + `eval` with an `IEvaluationContext`, integrated into reflection and query API.
 - Ensure PSR-2 code guidelines adherence.
 - Fixed binary operation `instanceof` compilation bug with literal class names.
 - Refactored caching implementation:
    - `Caching\IQueryCache` now acts as a wrapper to `Caching\ICacheAdapter`.
    - Any type of value can be cached and retrieved through the cache adapter.
    - Implemented cache namespacing API.
 - Fixed issue with `ITraversable::union` not reindexing keys.
 - Fixed issue with `Iterators\Common\Set` not detecting null values.

2.1.1 (22/5/14)
===============
 - Fix join iterator not rewinding outer value iterator on rewind

2.1.0 (7/5/14)
==============
 - Added `Providers\Loadable\Provider`. Performs queries in memory if the appropriate data has already been loaded
 - Fix `orderBy[Ascending|Descending]` on `Queryable` not converting function to an expression tree
 - Fix for internal variadic function parsing
 - Various docblock and formatting updates

2.0.0 (24/4/14)
===============
 - Migrated function / variable and constant names to PSR-2 standard.
