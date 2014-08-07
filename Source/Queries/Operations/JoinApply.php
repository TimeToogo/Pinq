<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\Common;
use Pinq\Queries\Functions;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class JoinApply extends Operation implements \Pinq\Queries\IOperation
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

    public function traverse(OperationVisitor $visitor)
    {
        $visitor->visitJoinApply($this);
    }
}
