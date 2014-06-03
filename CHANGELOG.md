dev-master
==========
 - Pass indexes to functions with the respective value as second parameter while
   maintaining support for single parameter internal functions
 - Proper support for non scalar keys:
    - Restructed conversion to array with ArrayCompatibleIterator, numerically reindexing all non scalar keys
    - Added ITraversable::iterate to iterate all unaltered values and keys
    - Added ITraversable::keys select the keys and ITraversable::reindex to reindex the values by their 0-based position
    - Added ITraversable::getTrueIterator to get the iterator for all values and unaltered keys
 - Refactored iterator structure, simplifying Iterator::next and Iterator::valid into Iterator::fetch
 - Removed IGroupedTraversable, use ITraversable::groupBy returning an array instead
 - ITraversable::groupBy implicitly indexes the value by the group key
 - Iterators\Utilities\Dictionary support array as keys with identity hash
 - Removed ITraversable::asQueryable, ITraversable::asRepository, updated interfaces annotations
   with covariant return types. ITraversable, ICollection, IQueryable, IRepository should all return
   their respective types for each query method.
 - Moved/Implemented Necessary interfaces (IOrdered*, IJoiningOn*, IJoiningTo*) with covariant 
   return types under Pinq\Interfaces namespace.
 - Traversable/Collection are now extendable.
 - Refactored ArrayExpression by creating ArrayItemExpression representing each element
 - Refactored OrderBy query segment by representing each function and direction as an OrderedFunction class
 - 

2.1.1 (22/5/14)
===============
 - Fix join iterator not rewinding outer value iterator on rewind

2.1.0 (7/5/14)
==============
 - Added Providers\Loadable\Provider. Performs queries in memory if the appropriate data has already been loaded
 - Fix orderBy[Ascending|Descending] on Queryable not converting function to an expression tree
 - Fix for internal variadic function parsing
 - Various docblock and formatting updates

2.0.0 (24/4/14)
===============
 - Migrated function / variable and constant names to PSR-2 standard.