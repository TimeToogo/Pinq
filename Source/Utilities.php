<?php

namespace Pinq;

final class Utilities
{
    private function __construct() { }

    public static $Identical = [__CLASS__, 'Identical'];

    public static function Identical($One, $Two)
    {
        return $One === $Two;
    }
}
