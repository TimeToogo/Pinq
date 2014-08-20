<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Queries\Common\Join;

/**
 * Implementation of the join options parser.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoinOptionsParser extends IJoinOptionsInterpretation, IQueryParser
{
    /**
     * @return Join\Options
     */
    public function getJoinOptions();
}
