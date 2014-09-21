<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\PinqException;
use Pinq\Providers\DSL;
use Pinq\Utilities;

/**
 * Implementation of the parameter hasher that returns a
 * unique hash based on a compiled request query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CompiledRequestQueryHasher implements IParameterHasher
{
    /**
     * @var O\IEvaluationContext|null
     */
    protected $evaluationContext;

    public function __construct(O\IEvaluationContext $evaluationContext = null)
    {
        $this->evaluationContext = $evaluationContext;
    }

    public function hash($value)
    {
        if (!($value instanceof IQueryable)) {
            throw new PinqException(
                    'Cannot get hash of compiled request query: expecting type of %s, %s given',
                    IQueryable::IQUERYABLE_TYPE,
                    Utilities::getTypeOrClass($value));
        }
        $provider = $value->getProvider();

        if (!($provider instanceof DSL\QueryProvider)) {
            throw new PinqException(
                    'Cannot get hash of compiled request query: invalid query provider, expecting type of %s, %s given',
                    DSL\QueryProvider::getType(),
                    Utilities::getTypeOrClass($value));
        }

        return $provider->getCompilerConfiguration()->getCompiledRequestQueryHash(
                $value->getExpression(),
                $this->evaluationContext
        );
    }
}
