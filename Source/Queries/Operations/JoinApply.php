<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\Common;
use Pinq\Queries\Functions;
use Pinq\Queries\IOperation;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoinApply extends Operation implements IOperation
{
    /**
     * The join options.
     *
     * @var Common\Join\Options
     */
    protected $options;

    /**
     * The function for selecting the resulting values of the join
     *
     * @var Functions\ConnectorMutator
     */
    protected $mutatorFunction;

    public function __construct(
            Common\Join\Options $options,
            Functions\ConnectorMutator $mutatorFunction
    ) {
        $this->options         = $options;
        $this->mutatorFunction = $mutatorFunction;
    }

    public function getType()
    {
        return self::JOIN_APPLY;
    }

    public function getParameters()
    {
        return array_merge($this->options->getParameters(), $this->mutatorFunction->getParameterIds());
    }

    /**
     * @return Common\Join\Options
     */
    final public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Functions\ConnectorMutator
     */
    final public function getMutatorFunction()
    {
        return $this->mutatorFunction;
    }

    public function traverse(IOperationVisitor $visitor)
    {
        return $visitor->visitJoinApply($this);
    }

    /**
     * @param Common\Join\Options        $options
     * @param Functions\ConnectorMutator $mutatorFunction
     *
     * @return JoinApply
     */
    public function update(
            Common\Join\Options $options,
            Functions\ConnectorMutator $mutatorFunction
    ) {
        if ($this->options === $options && $this->mutatorFunction === $mutatorFunction) {
            return $this;
        }

        return new self($options, $mutatorFunction);
    }
}
