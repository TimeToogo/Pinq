<?php

namespace Pinq\Parsing;

/**
 * Implementation of the located function interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LocatedFunction implements ILocatedFunction
{
    /**
     * @var IFunctionSignature
     */
    protected $signature;

    /**
     * @var IFunctionLocation
     */
    protected $location;

    /**
     * @var string
     */
    protected $locationAndSignatureHash;

    public function __construct(
            IFunctionSignature $signature,
            IFunctionLocation $location
    ) {
        $this->signature = $signature;
        $this->location  = $location;

        $this->locationAndSignatureHash = implode(
                '~',
                [
                        $this->signature->getHash(),
                        $this->location->getHash(),
                ]
        );
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getLocationAndSignatureHash()
    {
        return $this->locationAndSignatureHash;
    }
}
