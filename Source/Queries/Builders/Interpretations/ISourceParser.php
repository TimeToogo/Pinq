<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\Common\ISource;

/**
 * Interface of the source parser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ISourceParser extends ISourceInterpretation, IQueryParser
{
    /**
     * @return ISource
     */
    public function getSource();
}
