<?php

namespace Pinq\Parsing;

/**
 * Implementation of the magic scopes interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MagicScopes implements IMagicScopes
{
    /**
     * @var string|null
     */
    private $selfClass;

    /**
     * @var string|null
     */
    private $selfClassConstant;

    /**
     * @var string|null
     */
    private $staticClass;

    /**
     * @var string|null
     */
    private $staticClassConstant;

    /**
     * @var string|null
     */
    private $parentClass;

    /**
     * @var string|null
     */
    private $parentClassConstant;

    public function __construct(
            $selfClass,
            $selfClassConstant,
            $staticClass,
            $staticClassConstant,
            $parentClass,
            $parentClassConstant
    ) {
        $this->selfClass           = $selfClass;
        $this->selfClassConstant   = $selfClassConstant;
        $this->staticClass         = $staticClass;
        $this->staticClassConstant = $staticClassConstant;
        $this->parentClass         = $parentClass;
        $this->parentClassConstant = $parentClassConstant;
    }

    public function getSelfClass()
    {
        return $this->selfClass;
    }

    public function getSelfClassConstant()
    {
        return $this->selfClassConstant;
    }

    public function getStaticClass()
    {
        return $this->staticClass;
    }

    public function getStaticClassConstant()
    {
        return $this->staticClassConstant;
    }

    public function getParentClass()
    {
        return $this->parentClass;
    }

    public function getParentClassConstant()
    {
        return $this->parentClassConstant;
    }
}
