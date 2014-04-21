<?php

namespace Pinq\Parsing\PHPParser\Visitors;

use \Pinq\Parsing\PHPParser\PHPParserResolvedValueNode;

/**
 * Replaces all applicable nodes with a custom constance value node,
 * to be converted simpler in the AST class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ConstantValueNodeReplacerVisitor extends \PHPParser_NodeVisitorAbstract
{
    public function leaveNode(\PHPParser_Node $Node)
    {
        $IsConstant = null;
        $Value = $this->GetConstantValue($Node, $IsConstant);

        if ($IsConstant) {
            return new PHPParserResolvedValueNode($Value);
        }
    }

    private function GetConstantValue(\PHPParser_Node $Node, &$IsConstant)
    {
        switch (true) {
            case $Node instanceof PHPParserResolvedValueNode:
                $Value = $Node->Value;
                break;

            case $Node instanceof \PHPParser_Node_Scalar_DNumber:
            case $Node instanceof \PHPParser_Node_Scalar_LNumber:
            case $Node instanceof \PHPParser_Node_Scalar_String:
                $Value = $Node->value;
                break;

            case $Node instanceof \PHPParser_Node_Expr_ConstFetch:
                $Value = constant((string) $Node->name);
                break;

            case $Node instanceof \PHPParser_Node_Expr_ClassConstFetch:
                if ($Node->class instanceof \PHPParser_Node_Expr) {
                    return;
                }
                $Value = constant((string) $Node->class . '::' . $Node->name);
                break;

            case $Node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if ($Node->class instanceof \PHPParser_Node_Expr || $Node->name instanceof \PHPParser_Node_Expr) {
                    $IsConstant = false;
                    return;
                }
                $ClassName = (string) $Node->class;
                $Value = $ClassName::${$Node->name};
                break;

            default:
                $IsConstant = false;

                return;
        }
        $IsConstant = true;

        return $Value;
    }
}
