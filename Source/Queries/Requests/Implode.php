<?php

namespace Pinq\Queries\Requests;

use Pinq\FunctionExpressionTree;

/**
 * Request query for a string of all the projected values
 * concatenated by the specified delimiter
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Implode extends ProjectionRequest
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @param string $delimiter
     */
    public function __construct($delimiter, FunctionExpressionTree $functionExpressionTree = null)
    {
        parent::__construct($functionExpressionTree);
        $this->delimiter = $delimiter;
    }

    public function getType()
    {
        return self::IMPLODE;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitImplode($this);
    }
}
