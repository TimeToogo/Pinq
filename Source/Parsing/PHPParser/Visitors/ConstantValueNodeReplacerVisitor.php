<?php

namespace Pinq\Parsing\PHPParser\Visitors;

use \Pinq\Parsing\PHPParser\PHPParserResolvedValueNode;

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
                return $Node->Value;

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
                if ($Node->class instanceof \PHPParser_Node_Expr) {
                    return;
                }
                $ClassName = (string) $Node->class;
                $Value = $ClassName::${$Node->name};
                break;

            case $Node instanceof \PHPParser_Node_Expr_Array:
                $Value = [];

                foreach ($Node->items as $Key => $Item) {
                    $IsKeyConstant = null;
                    $IsValueConstant = null;

                    $Key = $Item->key === null ? null : $this->GetConstantValue($Item->key, $IsKeyConstant);
                    $ItemValue = $this->GetConstantValue($Item->value, $IsValueConstant);

                    if (!$IsKeyConstant || !$IsValueConstant) {
                        $IsConstant = false;

                        return;
                    }

                    if ($Key !== null) {
                        $Value[$Key] = $ItemValue;
                    } 
                    else {
                        $Value[] = $ItemValue;
                    }
                }
                break;

            default:
                $IsConstant = false;

                return;
        }
        $IsConstant = true;

        return $Value;
    }
}
