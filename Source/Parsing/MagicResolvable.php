<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Implementation of the function magic interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class MagicResolvable implements IMagicResolvable
{
    /**
     * @var O\Expression[]
     */
    private $resolvableExpressions;

    protected function __construct(array $resolvableExpressions)
    {
        $this->resolvableExpressions = $resolvableExpressions;
    }

    final public function resolveMagic(IFunctionMagic $functionMagic)
    {
        $resolvedExpressions = Resolvers\FunctionMagicResolver::resolve($functionMagic, $this->resolvableExpressions);

        return $this->withResolvedMagic($resolvedExpressions);
    }

    abstract protected function withResolvedMagic(array $resolvedExpressions);
}
