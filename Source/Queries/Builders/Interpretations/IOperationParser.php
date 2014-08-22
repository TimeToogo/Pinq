<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\IOperation;

/**
 * Interface of the operation parser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationParser extends IOperationInterpretation, IQueryParser
{
    /**
     * @return IOperation
     */
    public function getOperation();
}
