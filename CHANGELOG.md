dev-master
==========
 - Pass values to query functions with their associated key as second parameter while
   maintaining support for single parameter internal functions
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
   with covariant return types. `ITraversable`, `ICollection, `IQueryable`, `IRepository` should all return
   their respective types for each query method.
 - Moved/Implemented Necessary interfaces (`IOrdered*`, `IJoiningOn*`, `IJoiningTo*`) with covariant 
   return types under `Interfaces` namespace.
 - Removed `IGroupedTraversable`, use `ITraversable::groupBy` returning an array instead.
 - `ITraversable::groupBy` implicitly indexes each group by the group key.
 - Renamed `ITraversable::exists` to `ITraversable::isEmpty`.
 - `Traversable`/`Collection` are now extendable.
 - Implemented optional default value for `ITraversable::join`/`ITraversable::groupJoin`:

        - ```php
        Traversable::from(range(1, 6))
            ->join(range(1, 20))
            ->on(function ($outer, $inner) { return $outer % 2 === 0 && $outer * 2 === $inner; })
            ->withDefault('<Odd>')
            ->to(function ($outer, $inner) { 
                return $outer . ':' . $inner;
            });
        ```
        Will produce: `['1:<Odd>', '2:4', '3:<Odd>', '4:8', '5:<Odd>', '6:12']`

 - Refactored `ArrayExpression` by creating `ArrayItemExpression` representing each element.
 - Refactored `Queries\Segments\OrderBy` query segment by representing each 
   function and direction as an `Queries\Segments\OrderFunction` class.
 - Renamed `Queries\Segments\Operation::getTraversable` to `getValues`
 - Refactored `Join` query segments / operations.
    - Refactored inheritance to compositon, extracted join filtering to interface `Queries\Common\Join\IFilter`.
    - Created base class for query segment and operation: `Queries\Common\Join\Base`.
    - Updated `Queries\Segments\Segment[Walker|Visitor]` and `Queries\Operation\Visitor` to match new structure.
    - Hence updated `Providers\Traversable\ScopeEvaluator` and `Providers\Collection\OperationEvaluator`.

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
