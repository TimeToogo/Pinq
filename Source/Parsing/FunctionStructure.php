<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Implementation of the function structure interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionStructure extends MagicResolvable implements IFunctionStructure
{
    /**
     * @var IFunctionDeclaration
     */
    protected $declaration;

    /**
     * @var O\Expression[]
     */
    protected $bodyExpressions;

    public function __construct(IFunctionDeclaration $declaration, array $bodyExpressions)
    {
        parent::__construct($bodyExpressions);

        $this->declaration     = $declaration;
        $this->bodyExpressions = $bodyExpressions;
    }

    protected function withResolvedMagic(array $resolvedExpressions)
    {
        return new self($this->declaration, $resolvedExpressions);
    }

    public function getDeclaration()
    {
        return $this->declaration;
    }

    public function getBodyExpressions()
    {
        return $this->bodyExpressions;
    }
}
