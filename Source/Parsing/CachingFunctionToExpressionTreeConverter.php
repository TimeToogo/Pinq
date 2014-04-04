<?php

namespace Pinq\Parsing;

use \Pinq\PinqException;
use \Doctrine\Common\Cache;

class CachingFunctionToExpressionTreeConverter extends FunctionToExpressionTreeConverter
{
    private $Cache;

    public function __construct(
            Cache $Cache,
            IParser $Parser) {
        parent::__construct($Parser);

        $this->Cache = $Cache;
    }

    public function ConvertAndResolve(callable $Function, \ReflectionFunctionAbstract $Reflection) {
        $FunctionHash = $this->FunctionHash($Reflection);

        $ExpressionTree = null;
        if ($this->Cache->Contains($FunctionHash)) {
            $ExpressionTree = $this->Cache->fetch($FunctionHash);
        }

        if (!($ExpressionTree instanceof \Pinq\FunctionExpressionTree)) {
            $ExpressionTree = $this->GetFunctionExpressionTree($Reflection);

            /*
             * Resolve all that can be currently resolved and save the expression tree with all
             * the unresolvable variables so it can be resolved with different values later
             */
            $ExpressionTree->Simplify();
            $this->Cache->save($FunctionHash, $ExpressionTree);
        }
        $ExpressionTree->SetOriginalFunction($Function);
        
        if ($ExpressionTree->HasUnresolvedVariables()) {
            /*
             * Simplify and resolve any remaining expressions that could not be resolved due
             * to unresolved variables
             */
            $this->Resolve($ExpressionTree, $Reflection->getStaticVariables(), []);
        }
        
        if ($ExpressionTree->HasUnresolvedVariables()) {
            throw InvalidFunctionException::ContainsUnresolvableVariables($Reflection, $ExpressionTree->GetUnresolvedVariables());
        }

        return $ExpressionTree;
    }

    private function FunctionHash(\ReflectionFunctionAbstract $Reflection)
    {
        return 'ExpressionTree-' . md5(implode(' ', [
            $Reflection->getFileName(),
            $Reflection->getNamespaceName(),
            $Reflection->getName(),
            $Reflection->getStartLine(),
            $Reflection->getEndLine()]));
    }
}
