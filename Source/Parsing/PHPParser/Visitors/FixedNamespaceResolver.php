<?php

namespace Pinq\Parsing\PHPParser\Visitors;

use PHPParser_Node_Name;

/**
 * Extends the included \PHPParser_NodeVisitor_NameResolver to
 * fix a case sensitivity bug for scoped class types.
 *
 * @link https://github.com/nikic/PHP-Parser/pull/121
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FixedNamespaceResolver extends \PHPParser_NodeVisitor_NameResolver
{
    protected function resolveClassName(PHPParser_Node_Name $name)
    {
        $type = strtolower($name);
        if($type === 'self' || $type === 'static' || $type === 'parent') {
            return $name;
        }

        return parent::resolveClassName($name);
    }
}
