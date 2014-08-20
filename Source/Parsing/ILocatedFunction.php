<?php

namespace Pinq\Parsing;

/**
 * Interface of a function whose signature and location has been found.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ILocatedFunction
{
    /**
     * Gets the function's signature.
     *
     * @return IFunctionSignature
     */
    public function getSignature();

    /**
     * Gets the function's location.
     *
     * @return IFunctionLocation
     */
    public function getLocation();

    /**
     * Gets a hash of the located function from its location and signature.
     *
     * @return string
     */
    public function getLocationAndSignatureHash();
}
