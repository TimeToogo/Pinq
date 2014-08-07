<?php

namespace Pinq;

/**
 * Enum for ordering direction
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Direction
{
    private function __construct()
    {

    }

    const ASCENDING  = \SORT_ASC;
    const DESCENDING = \SORT_DESC;
}
