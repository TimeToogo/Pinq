<?php

namespace Pinq\Queries;

/**
 * Implementation of the source info interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SourceInfo implements ISourceInfo
{
    /**
     * @var string
     */
    private $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function equals(ISourceInfo $sourceInfo)
    {
        return get_class($this) === get_class($sourceInfo) && $this->hash === $sourceInfo->getHash();
    }
}
