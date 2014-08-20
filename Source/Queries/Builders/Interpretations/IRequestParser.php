<?php
namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\IRequest;

/**
 * Interface of the request parser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestParser extends IRequestInterpretation, IQueryParser
{
    /**
     * @return IRequest
     */
    public function getRequest();
}
