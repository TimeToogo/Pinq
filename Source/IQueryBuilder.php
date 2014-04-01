<?php

namespace Pinq;

/**
 * Interface for building a query stream from a series of method calls.
 */
interface IQueryBuilder
{
    /**
     * @return void
     */
    public function ClearStream();

    /**
     * @return boolean
     */
    public function IsEmpty();

    /**
     * @return Queries\IQueryStream
     */
    public function GetStream();
    
    public function Where(callable $Predicate);

    public function OrderBy(callable $Function);

    public function OrderByDescending(callable $Function);

    public function ThenBy(callable $Function);

    public function ThenByDescending(callable $Function);

    public function Skip($Amount);

    public function Take($Amount);

    public function Slice($Start, $Amount);

    public function IndexBy(callable $Function);

    public function GroupBy(callable $Function);

    public function Unique();

    public function Select(callable $Function);

    public function SelectMany(callable $Function);

    public function Union(ITraversable $Traversable);

    public function Append(ITraversable $Traversable);

    public function Intersect(ITraversable $Traversable);

    public function Except(ITraversable $Traversable);
}
