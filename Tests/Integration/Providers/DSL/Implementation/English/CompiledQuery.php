<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Queries;

class CompiledQuery extends Compilation\CompiledQuery implements Compilation\ICompiledRequest, Compilation\ICompiledOperation
{
    /**
     * @var string
     */
    private $english = '';

    public function __construct($english = '')
    {
        parent::__construct(Parameters\ParameterRegistry::none());
        $this->english = $english;
    }

    /**
     * @return string
     */
    public function getEnglish()
    {
        return $this->english;
    }

    public function __toString()
    {
        return $this->english;
    }
}
