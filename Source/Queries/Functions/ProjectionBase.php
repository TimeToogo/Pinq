<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Base class of a projection function (returns a value).
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ProjectionBase extends FunctionBase
{
    /**
     * @var O\ReturnExpression|null
     */
    protected $returnExpression;

    /**
     * @var O\Expression|null
     */
    protected $returnValueExpression;

    protected function initialize()
    {
        if (!$this->isInternal()) {
            foreach ($this->bodyExpressions as $statement) {
                if ($statement instanceof O\ReturnExpression) {
                    $this->returnExpression      = $statement;
                    $this->returnValueExpression = $statement->getValue();
                    break;
                }
            }
        }
    }

    protected function dataToSerialize()
    {
        return [$this->returnExpression, $this->returnValueExpression];
    }

    protected function unserializeData($data)
    {
        list($this->returnExpression, $this->returnValueExpression) = $data;
    }

    /**
     * @return boolean
     */
    final public function hasReturnExpression()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return $this->returnExpression !== null;
    }

    /**
     * @return O\ReturnExpression|null
     */
    final public function getReturnExpression()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return $this->returnExpression;
    }

    /**
     * @return boolean
     */
    final public function hasReturnValueExpression()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return $this->returnValueExpression !== null;
    }

    /**
     * @return O\Expression|null
     */
    final public function getReturnValueExpression()
    {
        $this->verifyNotInternal(__FUNCTION__);

        return $this->returnValueExpression;
    }
}
