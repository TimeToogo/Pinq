<?php

namespace Pinq\Parsing\PhpParser;

use Pinq\Parsing\IFunctionDeclaration;
use Pinq\Parsing\IFunctionLocation;
use Pinq\Parsing\IFunctionSignature;
use Pinq\Parsing\LocatedFunction;
use PhpParser\Node;

class LocatedFunctionNode extends LocatedFunction
{
    /**
     * @var IFunctionDeclaration
     */
    private $declaration;

    /**
     * @var Node\Stmt\Function_|Node\Stmt\ClassMethod|Node\Expr\Closure
     */
    private $node;

    public function __construct(
            IFunctionSignature $signature,
            IFunctionLocation $location,
            IFunctionDeclaration $declaration,
            Node $node
    ) {
        parent::__construct($signature, $location);

        $this->declaration = $declaration;
        $this->node        = $node;
    }

    /**
     * @return IFunctionDeclaration
     */
    public function getDeclaration()
    {
        return $this->declaration;
    }

    /**
     * @return Node[]
     */
    public function getBodyNodes()
    {
        return $this->node->stmts;
    }
}
