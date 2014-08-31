<?php

namespace Pinq\Parsing\PHPParser\Visitors;

use PhpParser\Node;
use PhpParser\NodeVisitor\NameResolver;

/**
 * Extends the included PhpParser\NodeVisitor\NameResolver to
 * fix a case sensitivity bug for scoped class types.
 *
 * @link https://github.com/nikic/PHP-Parser/pull/121
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FixedNamespaceResolver extends NameResolver
{
    protected function resolveClassName(Node\Name $name)
    {
        $type = strtolower($name);
        if ($type === 'self' || $type === 'static' || $type === 'parent') {
            return $name;
        }

        return parent::resolveClassName($name);
    }
}
