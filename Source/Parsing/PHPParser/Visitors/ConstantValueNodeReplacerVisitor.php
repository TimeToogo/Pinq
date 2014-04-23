<?php

namespace Pinq\Parsing\PHPParser\Visitors;

use Pinq\Parsing\PHPParser\PHPParserResolvedValueNode;

/**
 * Replaces all applicable nodes with a custom constance value node,
 * to be converted simpler in the AST class.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ConstantValueNodeReplacerVisitor extends \PHPParser_NodeVisitorAbstract
{
    public function leaveNode(\PHPParser_Node $node)
    {
        $isConstant = null;
        $value = $this->getConstantValue($node, $isConstant);

        if ($isConstant) {
            return new PHPParserResolvedValueNode($value);
        }
    }

    private function getConstantValue(\PHPParser_Node $node, &$isConstant)
    {
        switch (true) {

            case $node instanceof PHPParserResolvedValueNode:
                $value = $node->value;
                break;

            case $node instanceof \PHPParser_Node_Scalar_DNumber:

            case $node instanceof \PHPParser_Node_Scalar_LNumber:

            case $node instanceof \PHPParser_Node_Scalar_String:
                $value = $node->value;
                break;

            case $node instanceof \PHPParser_Node_Expr_ConstFetch:
                $value = constant((string)$node->name);
                break;

            case $node instanceof \PHPParser_Node_Expr_ClassConstFetch:
                if ($node->class instanceof \PHPParser_Node_Expr) {
                    return;
                }

                $value = constant((string)$node->class . '::' . $node->name);
                break;

            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if ($node->class instanceof \PHPParser_Node_Expr || $node->name instanceof \PHPParser_Node_Expr) {
                    $isConstant = false;

                    return;
                }

                $className = (string)$node->class;
                $value = $className::${$node->name};
                break;

            default:
                $isConstant = false;

                return;
        }
        $isConstant = true;

        return $value;
    }
}
