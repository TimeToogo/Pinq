<?php

namespace Pinq\Parsing\PHPParser;

use Pinq\Parsing\IFunctionDeclaration;
use Pinq\Parsing\IFunctionLocation;
use Pinq\Parsing\IFunctionSignature;
use Pinq\Parsing\LocatedFunction;

class LocatedFunctionNode extends LocatedFunction
{
    /**
     * @var IFunctionDeclaration
     */
    private $declaration;

    /**
     * @var PHPParser_Node_Stmt_Function|PHPParser_Node_Stmt_ClassMethod|PHPParser_Node_Expr_Closure
     */
    private $node;

    public function __construct(
            IFunctionSignature $signature,
            IFunctionLocation $location,
            IFunctionDeclaration $declaration,
            \PHPParser_Node $node
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
     * @return \PHPParser_Node[]
     */
    public function getBodyNodes()
    {
        return $this->node->stmts;
    }
}
