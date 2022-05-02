<?php

namespace Pinq\Expressions;

/**
 * Implementation of the expression evaluator using a variable
 * from the variable table or from the standard superglobals.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class VariableEvaluator extends Evaluator
{
    /**
     * @var string
     */
    protected $name;

    public function __construct($name, IEvaluationContext $context = null)
    {
        parent::__construct($context);

        $this->name = $name;
    }

    protected function doEvaluation(array $variableTable)
    {
        $variableTable += [
                'GLOBALS'  => $GLOBALS,
                '_SERVER'  => &$_SERVER,
                '_ENV'     => &$_ENV,
                '_REQUEST' => &$_REQUEST,
                '_GET'     => &$_GET,
                '_POST'    => &$_POST,
                '_COOKIE'  => &$_COOKIE,
                '_SESSION' => &$_SESSION,
                '_FILES'   => &$_FILES,
        ];

        return $variableTable[$this->name];
    }

    protected function doEvaluationWithNewThis(array $variableTable, $newThis)
    {
        return $this->doEvaluation($variableTable);
    }
}
