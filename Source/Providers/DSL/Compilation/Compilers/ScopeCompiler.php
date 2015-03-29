<?php

namespace Pinq\Providers\DSL\Compilation\Compilers;

use Pinq\Providers\DSL\Compilation\IQueryCompilation;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Queries;
use Pinq\Queries\Segments;

/**
 * Implementation of the scope compiler using the visitor pattern.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ScopeCompiler extends Compiler implements IScopeCompiler
{
    /**
     * @var Queries\IScope
     */
    protected $scope;

    public function __construct(Queries\IScope $scope, IQueryCompilation $compilation)
    {
        parent::__construct($compilation);
        $this->scope = $scope;
    }

    /**
     * @return Queries\IScope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return mixed
     */
    public function compile()
    {
        foreach ($this->scope->getSegments() as $segment) {
            $segment->traverse($this);
        }
    }
}
